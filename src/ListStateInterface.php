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

interface ListStateInterface
{
    public function getPage(): int;

    public function getLimit(): int;

    public function getFilters(): array;

    public function getSorter(): array;
}
