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

class ListState implements ListStateInterface, \JsonSerializable
{
    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var array
     */
    private $filters = array();

    /**
     * @var array
     */
    private $sorter = array();

    public function __construct(int $page, int $limit)
    {
        $this->page = $page;
        $this->limit = $limit;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return ListState
     */
    public function setFilters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function getSorter(): array
    {
        return $this->sorter;
    }

    public function setSorter(array $sorter): self
    {
        $this->sorter = $sorter;

        return $this;
    }

    public function toArray(): array
    {
        return array(
            'page' => $this->getPage(),
            'limit' => $this->getLimit(),
            'filters' => $this->getFilters(),
            'sorter' => $this->getSorter(),
        );
    }

    public function fromArray(array $data): self
    {
        if (isset($data['page'])) {
            $this->setPage((int) $data['page']);
        }

        if (isset($data['limit'])) {
            $this->setLimit((int) $data['limit']);
        }

        if (isset($data['filters'])) {
            $this->setFilters((array) $data['filters']);
        }

        if (isset($data['sorter'])) {
            $this->setSorter((array) $data['sorter']);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
