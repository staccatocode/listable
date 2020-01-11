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
     * Add element (filter, field) to builder.
     */
    public function add(string $name, string $type, array $options = []): self;

    /**
     * Check if builder already has element (filter, field).
     */
    public function has(string $name): bool;

    /**
     * Remove element (filter, field) from builder.
     */
    public function remove(string $name): self;

    /**
     * Create new list based on current builder configuration.
     */
    public function getList(): ListInterface;

    /**
     * Return list config.
     */
    public function getListConfig(): ListConfigInterface;
}
