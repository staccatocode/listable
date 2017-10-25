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

interface ListBuilderInterface extends ListConfigBuilderInterface
{
    /**
     * Create new list based on current builder
     * configuration.
     *
     * @return ListInterface
     */
    public function getList(): ListInterface;

    /**
     * Return list config.
     *
     * @return ListConfigInterface
     */
    public function getListConfig(): ListConfigInterface;
}
