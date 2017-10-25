<?php

/*
 * This file is part of staccato listable component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\Listable\Repository;

abstract class AbstractRepository
{
    /**
     * Ordering constant definitions.
     */
    const ORDER_ASC = 'asc';

    const ORDER_DESC = 'desc';

    /**
     * @var array
     */
    protected $filters = array();

    /**
     * @var array
     */
    protected $sorter = array(
        'name' => null,
        'type' => self::ORDER_ASC,
    );

    /**
     * Find matching objects and compose list.
     *
     * @param int $limit limit of objects per page (0 = no limit)
     * @param int $page  find page
     *
     * @return mixed result set
     */
    abstract public function find(int $limit = 0, int $page = 0);

    /**
     * Count number of matching objects.
     *
     * @return int
     */
    abstract public function count(): int;

    /**
     * Set new list filter.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return AbstractRepository self
     */
    public function filterBy(string $name, $value): AbstractRepository
    {
        $this->filters[$name] = $value;

        return $this;
    }

    /**
     * Set list filters.
     *
     * @param array $filters
     *
     * @return AbstractRepository self
     */
    public function setFilters(array $filters): AbstractRepository
    {
        foreach ($filters as $f => $v) {
            $this->filterBy($f, $v);
        }

        return $this;
    }

    /**
     * Set list ordering.
     *
     * @param string|null $name sorter name or null to disable
     * @param string      $type `asc` or `desc`
     *
     * @return AbstractRepository self
     */
    public function orderBy(?string $name, string $type = self::ORDER_ASC): AbstractRepository
    {
        $this->sorter['name'] = $name;
        $this->sorter['type'] = $type;

        return $this;
    }
}
