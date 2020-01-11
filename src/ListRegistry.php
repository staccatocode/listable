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
use Staccato\Component\Listable\Field\AbstractField;
use Staccato\Component\Listable\Filter\AbstractFilter;
use Staccato\Component\Listable\Repository\AbstractRepository;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ListRegistry implements ListRegistryInterface
{
    /** @var ServiceLocator[] */
    private $locators;

    /**
     * ListRegistry constructor.
     *
     * @param ServiceLocator[] $locators
     */
    public function __construct(array $locators = [])
    {
        $this->locators = $locators;
    }

    /**
     * {@inheritdoc}
     */
    public function getListType(string $name): ListTypeInterface
    {
        return $this->getInstance($name, ListTypeInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldType(string $name): AbstractField
    {
        return $this->getInstance($name, AbstractField::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getStateProvider(string $name): ListStateProviderInterface
    {
        return $this->getInstance($name, ListStateProviderInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterType(string $name): AbstractFilter
    {
        return $this->getInstance($name, AbstractFilter::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(string $name, array $options = []): AbstractRepository
    {
        return $this->getInstance($name, AbstractRepository::class)->setOptions($options);
    }

    /**
     * @return mixed
     */
    protected function getInstance(string $name, string $base)
    {
        if (isset($this->locators[$base]) && $this->locators[$base]->has($name)) {
            return $this->locators[$base]->get($name);
        }

        if (class_exists($name) && is_subclass_of($name, $base)) {
            return new $name();
        }

        throw new InvalidArgumentException(sprintf('Could not get "%s". Class or service does not exists or is not subclass of "%s"?', $name, $base));
    }
}
