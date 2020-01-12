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

interface ListFactoryInterface
{
    /**
     * Creates new instance of ListInterface based on given list type and options.
     *
     * @param string $listType class of list type
     * @param array  $options  options passed to list type
     */
    public function create(string $listType, array $options = []): ListInterface;
}
