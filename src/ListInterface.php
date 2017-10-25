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

interface ListInterface extends ListConfigInterface
{
    /**
     * Return loaded data.
     *
     * @return array|Traversable
     */
    public function getData();

    /**
     * Return current page.
     *
     * @return int
     */
    public function getPage(): int;

    /**
     * Return total number of pages.
     *
     * @return int
     */
    public function countPages(): int;

    /**
     * Return total number of rows.
     *
     * @return int
     */
    public function count(): int;

    /**
     * Loads list data.
     *
     * @return ListInterface self
     */
    public function load(): ListInterface;

    /**
     * Handle actions on list.
     *
     * @param string   $action  action name
     * @param callable $handler callable action handler
     *
     * @return ListInterface self
     */
    public function on($action, callable $handler): ListInterface;

    /**
     * Return instance of list view object.
     *
     * @return ListView
     */
    public function createView(): ListView;
}
