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

interface ListConfigInterface
{
    /**
     * Returns name (ID) of the list.
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
     */
    public function getPageParam(): string;

    /**
     * Returns sorter HTTP query parameter name.
     */
    public function getSorterParam(): string;

    /**
     * Returns sorter HTTP query parameters.
     */
    public function getSorter(): array;

    /**
     * Returns limit of rows per page.
     */
    public function getLimit(): int;

    /**
     * Returns HTTP query limit name parameter.
     */
    public function getLimitParam(): string;

    /**
     * Returns additional limit param options like min and max allowed value.
     */
    public function getLimitParamOptions(): array;

    /**
     * Returns HTTP query filters name parameter.
     */
    public function getFiltersParam(): string;

    /**
     * Return filters definition.
     *
     * @return AbstractFilter[]
     */
    public function getFilters(): array;

    /**
     * Return filters definition.
     *
     * @return AbstractField[]
     */
    public function getFields(): array;

    /**
     * Returns persist state.
     */
    public function getPersistState(): bool;

    /**
     * Returns currently set list repository.
     */
    public function getRepository(): ?AbstractRepository;

    /**
     * Returns type list was created from.
     */
    public function getType(): ?ListTypeInterface;

    /**
     * Return list type options.
     */
    public function getOptions(): array;
}
