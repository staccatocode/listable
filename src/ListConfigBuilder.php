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

use Staccato\Component\Listable\Repository\AbstractRepository;

class ListConfigBuilder implements ListConfigBuilderInterface
{
    /**
     * Repository that will be used to
     * load data to the list.
     *
     * @var AbstractRepository
     */
    protected $repository;

    /**
     * Name (ID) of the list.
     *
     * @var string
     */
    private $name = 'list';

    /**
     * Page of the list.
     *
     * @var int
     */
    private $page = 0;

    /**
     * Name of query page parameter for the list.
     *
     * @var string
     */
    private $pageParam = '';

    /**
     * Limit of objects per page that
     * will be passed to the repository.
     *
     * @var int
     */
    private $limit = 0;

    /**
     * Name of query limit parameter for the list.
     *
     * @var string
     */
    private $limitParam = '';

    /**
     * Limit min and max options.
     *
     * @var array
     */
    private $limitParamOptions = array();

    /**
     * Filters that will be passed to the repository.
     *
     * @var array
     */
    private $filters = array();

    /**
     * Filters that will be not overriden by
     * request filters.
     *
     * @var array
     */
    private $filtersLock = array();

    /**
     * Filters source.
     *
     * @var string
     */
    private $filterSource = 'get';

    /**
     * Sorter that will be passed to the repository.
     *
     * @var array
     */
    private $sorter = array();

    /**
     * Sorter query parameters names for the list.
     *
     * @var array
     */
    private $sorterParams = array(
        'asc' => '',
        'desc' => '',
    );

    /**
     * Action param name.
     *
     * @var string
     */
    private $actionParam = 'st_list';

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return (string) $this->name;
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
    public function getFilterSource(): string
    {
        return (string) $this->filterSource;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilterSource(string $source): ListConfigBuilderInterface
    {
        $this->filterSource = strtolower($source);

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
    public function getPageParam(): string
    {
        return (string) $this->pageParam;
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
        $min = isset($this->limitParamOptions['min']) ? (int) $this->limitParamOptions['min'] : 0;
        $max = isset($this->limitParamOptions['max']) ? (int) $this->limitParamOptions['max'] : (int) $this->limit;

        $limit = max((int) $this->limit, $min);
        $limit = min($limit, $max);

        return $limit;
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
    public function setLimit(int $limit): ListConfigBuilderInterface
    {
        $this->limit = $limit > 0 ? $limit : 0;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimitParam(?string $name, array $options = array()): ListConfigBuilderInterface
    {
        $this->limitParam = (string) $name;
        $this->limitParamOptions = $options;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSorterParams(): array
    {
        return $this->sorterParams;
    }

    /**
     * {@inheritdoc}
     */
    public function setSorterParams(?string $asc, ?string $desc): ListConfigBuilderInterface
    {
        $this->sorterParams = array(
            'asc' => (string) $asc,
            'desc' => (string) $desc,
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getActionParam(): string
    {
        return $this->actionParam;
    }

    /**
     * {@inheritdoc}
     */
    public function setActionParam(?string $name): ListConfigBuilderInterface
    {
        $this->actionParam = (string) $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSorter(?string $name, ?string $type): ListConfigBuilderInterface
    {
        $this->sorter = array(
            'name' => (string) $name,
            'type' => strtolower($type),
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilter(string $name, $value, bool $locked = false): ListConfigBuilderInterface
    {
        if (!$this->isFilterLocked($name)) {
            $this->filters[$name] = $value;
            $this->filtersLock[$name] = $locked;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilters(array $filters, bool $locked = false): ListConfigBuilderInterface
    {
        foreach ($filters as $name => $value) {
            $this->setFilter($name, $value, $locked);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        return array(
            'filters' => $this->filters,
            'sorter' => $this->sorter,
        );
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
    public function setRepository(AbstractRepository $repository): ListConfigBuilderInterface
    {
        $this->repository = $repository;

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
     * Check whether filter is locked.
     *
     * @param string $name The name of the filter
     *
     * @return bool
     */
    protected function isFilterLocked(string $name): bool
    {
        return isset($this->filtersLock[$name]) &&
                     $this->filtersLock[$name] ? true : false;
    }
}
