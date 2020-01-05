<?php

/*
 * This file is part of staccato listable component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\Listable;

use Staccato\Component\Listable\Exception\InvalidArgumentException;
use Staccato\Component\Listable\Repository\Result;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class ListBuilder extends ListConfigBuilder implements ListBuilderInterface
{
    /** @var array */
    private $elements = array();

    /**
     * {@inheritdoc}
     */
    public function add(string $name, string $type, array $options = array()): ListBuilderInterface
    {
        $this->elements[$name] = [$type, $options];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->elements);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $name): ListBuilderInterface
    {
        if ($this->has($name)) {
            unset($this->elements[$name]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(): ListInterface
    {
        $this->resolveElements();

        $state = $this->getState();
        $list = new Listable($this->getListConfig(), $state);

        $repository = $this->getRepository();
        if (!$repository) {
            return $list;
        }

        do {
            $result = $repository->getResult($state);

            $list
                ->setTotalCount($result->getTotalCount())
                ->setData($this->getDataFromResult($result))
            ;

            if ($state->getLimit()) {
                $list->setTotalPages(ceil($result->getTotalCount() / $state->getLimit()));
            } else {
                $list->setTotalPages($list->getTotalCount() > 0 ? 1 : 0);
            }

            $list->setPage(min($list->getTotalPages() > 0 ? $list->getTotalPages() - 1 : 0, $state->getPage()));

            $pageOverflow = $list->checkPageOverflow();
            if ($pageOverflow) {
                $state->setPage($list->getPage());
            }
        } while ($pageOverflow);

        return $list;
    }

    private function getState()
    {
        $state = $this->registry->getStateProvider($this->getStateProvider())->getState($this);

        $options = $this->getLimitParamOptions();
        $min = $options['min'] ?? 0;
        $max = $options['max'] ?? $state->getLimit();

        $state->setLimit(min(max($state->getLimit(), $min), $max));

        return $state;
    }

    private function getDataFromResult(Result $result): array
    {
        $propertyAccessor = new PropertyAccessor();
        $data = array();
        foreach ($result->getRows() as $item) {
            $row = array();
            $propertyPathFormat = \is_array($item) || $item instanceof \ArrayAccess ? '[%s]' : '%s';
            foreach ($this->getFields() as $name => $field) {
                $propertyPath = $field->getPropertyPath() ?? sprintf($propertyPathFormat, $name);
                $row[$name] = $propertyAccessor->getValue($item, $propertyPath);
            }
            $data[] = $row;
        }

        return $data;
    }

    private function resolveElements(): void
    {
        foreach ($this->elements as $name => [$type, $options]) {
            try {
                $this->setField($name, $type, $options);
                unset($this->elements[$name]);
                continue;
            } catch (InvalidArgumentException $e) {
            }

            try {
                $this->setFilter($name, $type, $options);
                unset($this->elements[$name]);
                continue;
            } catch (InvalidArgumentException $e) {
            }

            throw new InvalidArgumentException(sprintf('Could not resolve unsupported element type `%s`.', $type));
        }
    }
}
