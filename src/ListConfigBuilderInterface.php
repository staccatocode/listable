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

interface ListConfigBuilderInterface extends ListConfigInterface
{
    /**
     * Set name (ID) of the list.
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): ListConfigBuilderInterface;

    /**
     * Set new filter.
     *
     * @param string $name   filter name
     * @param mixed  $value  filter value
     * @param bool   $locked if true, filter should never be overwritten
     *                       by requested filters
     *
     * @return self
     */
    public function setFilter(string $name, $value, bool $locked = false): ListConfigBuilderInterface;

    /**
     * Set new filters.
     *
     * @param array $filters array of filters
     * @param bool  $locked  if true, filter should never be overwritten
     *                       by requested filters
     *
     * @return self
     */
    public function setFilters(array $filters, bool $locked = false): ListConfigBuilderInterface;

    /**
     * Set filter source.
     *
     * @param string $source source of the filters (session, get)
     *
     * @return self
     */
    public function setFilterSource(string $source): ListConfigBuilderInterface;

    /**
     * Set list page.
     *
     * @param int $page
     *
     * @return self
     */
    public function setPage(int $page): ListConfigBuilderInterface;

    /**
     * Set request query page parameter name.
     *
     * @param string|null $name query parameter name
     *
     * @return self
     */
    public function setPageParam(?string $name): ListConfigBuilderInterface;

    /**
     * Set default list sorter.
     *
     * The sorter can be overriden by HTTP query
     * sorter parameters defined by setSorterParams method.
     *
     * @param string $name name of the sorter
     * @param string $type type of sorter (asc or desc)
     *
     * @return self
     */
    public function setSorter(?string $name, ?string $type): ListConfigBuilderInterface;

    /**
     * Set names of asc and desc HTTP query parameters.
     *
     * @param string $asc  query parameter name
     * @param string $desc query parameter name
     *
     * @return self
     */
    public function setSorterParams(?string $asc, ?string $desc): ListConfigBuilderInterface;

    /**
     * Set limit of objects per page.
     *
     * @param int $limit
     *
     * @return self
     */
    public function setLimit(int $limit): ListConfigBuilderInterface;

    /**
     * Set request query limit parameter name.
     *
     * @param string|null $name query parameter name
     *
     * @return self
     */
    public function setLimitParam(?string $name): ListConfigBuilderInterface;

    /**
     * Set list repository.
     *
     * @param AbstractRepository $repository
     *
     * @return self
     */
    public function setRepository(AbstractRepository $repository): ListConfigBuilderInterface;
}
