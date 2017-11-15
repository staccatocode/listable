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

interface ListConfigInterface
{
    /**
     * Returns name (ID) of the list.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns current page.
     *
     * @return int If null then no page was set
     */
    public function getPage(): int;

    /**
     * Returns HTTP query page name parameter.
     *
     * @return string
     */
    public function getPageParam(): string;

    /**
     * Returns filter source.
     *
     * @return string
     */
    public function getFilterSource(): string;

    /**
     * Returns asc and desc parameter names.
     *
     * @return array
     */
    public function getSorterParams(): array;

    /**
     * Returns limit of rows per page.
     *
     * @return int
     */
    public function getLimit(): int;

    /**
     * Returns HTTP query limit name parameter.
     *
     * @return string
     */
    public function getLimitParam(): string;

    /**
     * Returns HTTP action param name.
     *
     * @return string
     */
    public function getActionParam(): string;

    /**
     * Returns currently set list repository.
     *
     * @return AbstractRepository|null
     */
    public function getRepository(): ?AbstractRepository;

    /**
     * Returns filters and sorter.
     *
     * return array
     */
    public function getOptions(): array;
}
