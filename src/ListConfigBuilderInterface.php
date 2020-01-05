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

interface ListConfigBuilderInterface extends ListConfigInterface
{
    /**
     * Sets name (ID) of the list.
     */
    public function setName(string $name): self;

    /**
     * Sets list page.
     */
    public function setPage(int $page): self;

    /**
     * Sets request query page parameter name.
     *
     * @param string|null $name query parameter name
     */
    public function setPageParam(?string $name): self;

    /**
     * Sets default list sorter.
     *
     * The sorter can be override by HTTP query
     * sorter parameters defined by setSorterParam method.
     *
     * @param string|null $name name of the sorter
     * @param string|null $type type of sorter (asc or desc)
     */
    public function setSorter(?string $name, ?string $type): self;

    /**
     * Sets names of sorter HTTP query parameters.
     */
    public function setSorterParam(?string $sorterParam): self;

    /**
     * Sets name of filters HTTP query parameters.
     */
    public function setFiltersParam(?string $filtersParam): self;

    /**
     * Sets limit of objects per page.
     */
    public function setLimit(int $limit): self;

    /**
     * Sets request query limit parameter name.
     *
     * @param string|null $name    query parameter name
     * @param array       $options min and max option
     */
    public function setLimitParam(?string $name, array $options = array()): self;

    /**
     * Sets if list should persist its state.
     */
    public function setPersistState(bool $persist): self;

    /**
     * Sets list repository.
     */
    public function setRepository(string $repositoryClass, array $options = array()): self;

    /**
     * Sets state provider.
     */
    public function setStateProvider(string $stateProvider): self;

    /**
     * Sets type the list was created from.
     */
    public function setType(ListTypeInterface $type): self;

    /**
     * Sets type options.
     */
    public function setOptions(array $options): self;
}
