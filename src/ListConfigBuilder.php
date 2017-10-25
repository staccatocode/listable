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
use Staccato\Component\Listable\Repository\Exception\InvalidRepositoryException;

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
     * @var int|null
     */
    private $page = null;

    /**
     * Name of query page parameter for the list.
     *
     * @var string
     */
    private $pageParam = 'page';

    /**
     * Limit of objects per page that
     * will be passed to the repository.
     *
     * @var int
     */
    private $limit = 0;

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
        'asc' => 'asc',
        'desc' => 'desc',
    );

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
    public function getFilterSource(): string
    {
        return $this->filterSource;
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
    public function getPage(): ?int
    {
        return $this->page;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageParam(): string
    {
        return $this->pageParam;
    }

    /**
     * {@inheritdoc}
     */
    public function setPage(int $page): ListConfigBuilderInterface
    {
        $this->page = (int) $page;
        $this->page = $this->page < 0 ? 0 : $this->page;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPageParam(string $name): ListConfigBuilderInterface
    {
        $this->pageParam = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit(int $limit): ListConfigBuilderInterface
    {
        $limit = (int) $limit;
        $this->limit = $limit > 0 ? $limit : 0;

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
    public function setSorterParams(string $asc, string $desc): ListConfigBuilderInterface
    {
        $this->sorterParams = array(
            'asc' => $asc,
            'desc' => $desc,
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSorter(?string $name, ?string $type): ListConfigBuilderInterface
    {
        $this->sorter = array(
            'name' => $name,
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
    public function setRepository($repository, array $arguments = array()): ListConfigBuilderInterface
    {
        if (is_string($repository)) {
            if (class_exists($repository)) {
                $repository = new $repository(...$arguments);
            }
        }

        if (!$repository instanceof AbstractRepository) {
            throw new InvalidRepositoryException(sprintf(
                'Loader argument must be instance of `%s` instance of `%s` given.',
                AbstractRepository::class, null === $repository ? 'NULL' : get_class($repository)
            ));
        }

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
