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
     * @param int    $default   query page parameter not exists
     */
    public function getPage(string $paramName, int $default = 0): int;

    /**
     * Return current limit per page.
     *
     * @param string $paramName query limit parameter name
     * @param int    $default   query limit parameter not exists
     */
    public function getLimit(string $paramName, int $default = 0): int;

    /**
     * Return filters associated to the list.
     *
     * @param string $paramName name of the list
     * @param array  $default   default value if filters not set
     */
    public function getFilters(string $paramName, array $default): array;

    /**
     * Return sorter values.
     *
     * @param string $paraName query name of sort parameter
     * @param array  $default  default value if sorter not set
     */
    public function getSorter(string $paraName, array $default): array;
}
