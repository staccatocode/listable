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

interface ListInterface
{
    /**
     * Return data loaded from list repository.
     */
    public function getData(): iterable;

    /**
     * Return list state.
     */
    public function getState(): ListStateInterface;

    /**
     * Return list config.
     */
    public function getConfig(): ListConfigInterface;

    /**
     * Return number of data page loaded from repository.
     */
    public function getPage(): int;

    /**
     * Return total number of pages found in repository.
     */
    public function getTotalPages(): int;

    /**
     * Return total number of elements in repository.
     */
    public function getTotalCount(): int;

    /**
     * Return instance of list view object.
     */
    public function createView(): ListView;
}
