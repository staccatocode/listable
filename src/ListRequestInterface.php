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

interface ListRequestInterface
{
    /**
     * Return current page number.
     *
     * @param string $paramName query page parameter name
     *
     * @return int
     */
    public function getPage(string $paramName): int;

    /**
     * Return filters associated to the list.
     *
     * @param string $paramName   name of the list
     * @param string $filterSoure source of the filter (session, get)
     *
     * @return array
     */
    public function getFilters(string $paramName, string $filterSource): array;

    /**
     * Return sorter values.
     *
     * @param string $paramAsc  query name of ascending parameter
     * @param string $paramDesc query name of descending parameter
     *
     * @return array
     */
    public function getSorter(string $paramAsc, string $paramDesc): array;

    /**
     * Store filters in filters storage.
     *
     * @param string $paramName list name associated to filters
     * @param array  $filters   filters to be stored
     *
     * @return self
     */
    public function storeFilters(string $paramName, array $filters): ListRequestInterface;
}
