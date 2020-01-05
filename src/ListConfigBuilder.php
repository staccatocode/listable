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

use Staccato\Component\Listable\Field\AbstractField;
use Staccato\Component\Listable\Filter\AbstractFilter;
use Staccato\Component\Listable\Repository\AbstractRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListConfigBuilder implements ListConfigBuilderInterface, \JsonSerializable
{
    /**
     * @var ListRegistryInterface
     */
    protected $registry;

    /**
     * @var AbstractRepository
     */
    private $repository;

    /**
     * @var string
     */
    private $name = 'list';

    /**
     * @var array
     */
    private $options = array();

    /**
     * @var int
     */
    private $page = 0;

    /**
     * @var string|null
     */
    private $pageParam;

    /**
     * @var int
     */
    private $limit = 0;

    /**
     * @var array
     */
    private $sorter = array();

    /**
     * @var string|null
     */
    private $sorterParam;

    /**
     * @var string|null
     */
    private $limitParam;

    /**
     * @var array
     */
    private $limitParamOptions = array();

    /**
     * @var AbstractFilter[]
     */
    private $filters = array();

    /**
     * @var string|null
     */
    private $filtersParam;

    /**
     * @var AbstractField[]
     */
    private $fields = array();

    /**
     * @var string
     */
    private $stateProvider = ListStateProvider::class;

    /**
     * @var bool
     */
    private $persistState = false;

    /**
     * @var ListTypeInterface|null;
     */
    private $type;

    public function __construct(ListRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): ListConfigBuilderInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFiltersParam(?string $filtersParam): ListConfigBuilderInterface
    {
        $this->filtersParam = $filtersParam;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiltersParam(): string
    {
        return (string) $this->filtersParam;
    }

    /**
     * {@inheritdoc}
     */
    public function getSorterParam(): string
    {
        return (string) $this->sorterParam;
    }

    /**
     * {@inheritdoc}
     */
    public function setSorterParam(?string $sorterParam): ListConfigBuilderInterface
    {
        $this->sorterParam = (string) $sorterParam;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options): ListConfigBuilderInterface
    {
        $this->options = $options;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPage(): int
    {
        return (int) $this->page;
    }

    /**
     * {@inheritdoc}
     */
    public function setPage(int $page): ListConfigBuilderInterface
    {
        $this->page = $page > 0 ? $page : 0;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageParam(): string
    {
        return (string) $this->pageParam;
    }

    /**
     * {@inheritdoc}
     */
    public function setPageParam(?string $name): ListConfigBuilderInterface
    {
        $this->pageParam = (string) $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit(): int
    {
        $min = $this->limitParamOptions['min'] ?? 0;
        $max = $this->limitParamOptions['max'] ?? $this->limit;

        $limit = max($this->limit, $min);
        $limit = min($limit, $max);

        return $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit(int $limit): ListConfigBuilderInterface
    {
        $this->limit = $limit > 0 ? $limit : 0;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimitParam(): string
    {
        return (string) $this->limitParam;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimitParamOptions(): array
    {
        return $this->limitParamOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimitParam(?string $name, array $options = array()): ListConfigBuilderInterface
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setDefaults(array(
                'min' => null,
                'max' => null,
            ))
            ->setAllowedTypes('min', array('int', 'null'))
            ->setAllowedTypes('max', array('int', 'null'))
        ;

        $this->limitParam = (string) $name;
        $this->limitParamOptions = $resolver->resolve($options);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSorter(?string $name, ?string $type): ListConfigBuilderInterface
    {
        if (null === $name) {
            $this->sorter = array();
        } elseif (null === $type) {
            unset($this->sorter[$name]);
        } else {
            $this->sorter[$name] = strtolower($type);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSorter(): array
    {
        return $this->sorter;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilter(string $name, string $type, array $options = array()): ListConfigBuilderInterface
    {
        $filter = $this->registry->getFilterType($type)->setOptions($options);

        $this->filters[$filter->getField() ?? $name] = $filter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setField(string $name, string $type, array $options = array()): ListConfigBuilderInterface
    {
        $field = $this->registry->getFieldType($type)->setOptions($options);

        if ($field->hasFilter()) {
            $this->setFilter($name, $field->getFilter(), array('field' => $name) + $field->getFilterOptions());
        }

        $this->fields[$name] = $field;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getStateProvider(): string
    {
        return $this->stateProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function setStateProvider(string $stateProvider): ListConfigBuilderInterface
    {
        $this->stateProvider = $stateProvider;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistState(): bool
    {
        return $this->persistState;
    }

    /**
     * {@inheritdoc}
     */
    public function setPersistState(bool $persist): ListConfigBuilderInterface
    {
        $this->persistState = $persist;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(): ?AbstractRepository
    {
        return $this->repository;
    }

    /**
     * {@inheritdoc}
     */
    public function setRepository(string $repositoryClass, array $options = array()): ListConfigBuilderInterface
    {
        $this->repository = $this->registry->getRepository($repositoryClass, $options);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): ?ListTypeInterface
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(ListTypeInterface $type): ListConfigBuilderInterface
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getListConfig(): ListConfigInterface
    {
        return clone $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $fields = array();
        $filters = array();

        foreach ($this->getFilters() as $key => $filter) {
            $filters[$key] = $filter->getOptions();
        }

        foreach ($this->getFields() as $key => $field) {
            $fields[$key] = $field->getOptions();
        }

        return array(
            'name' => $this->getName(),
            'persist_state' => $this->getPersistState(),
            'page_param' => $this->getPageParam(),
            'limit_param' => $this->getLimitParam(),
            'limit_param_options' => $this->getLimitParamOptions(),
            'filters_param' => $this->getFiltersParam(),
            'fields' => $fields,
            'filters' => $filters,
        );
    }
}
