<?php

/*
 * This file is part of staccato list component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\ListLoader\Repository;

abstract class ListableRepository
{
    /**
     * @var array
     */
    protected $filters = array();

    /**
     * @var array
     */
    protected $sorter = array(
        'name' => null,
        'type' => 'asc',
    );

    /**
     * Find matching objects and compose list.
     *
     * @param number $limit limit of objects per page (0 = no limit)
     * @param number $page  find page
     *
     * @return mixed result set
     */
    abstract public function find($limit = 0, $page = 0);

    /**
     * Count number of matching objects.
     * 
     * @return int
     */
    abstract public function count();

    /**
     * Set new filter.
     * 
     * @param unknown $name
     * @param unknown $value
     * 
     * @return self
     */
    public function filterBy($name, $value)
    {
        $this->filters[$name] = $value;

        return $this;
    }

    /**
     * Set filters.
     * 
     * @param array $filters
     * 
     * @return self
     */
    public function setFilters(array $filters)
    {
        foreach ($filters as $f => $v) {
            $this->filterBy($f, $v);
        }

        return $this;
    }

    /**
     * Order list.
     * 
     * @param string|null $name sorter name
     * @param string      $type asc or desc
     * 
     * @return ListableRepository
     */
    public function orderBy($name, $type)
    {
        $this->sorter['name'] = $name;
        $this->sorter['type'] = $type;

        return $this;
    }
}
