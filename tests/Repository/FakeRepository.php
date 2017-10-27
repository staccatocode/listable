<?php

/*
 * This file is part of staccato listable component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\Listable\Tests\Repository;

use Staccato\Component\Listable\Repository\AbstractRepository;

class FakeRepository extends AbstractRepository
{
    public $arguments = array();

    public function __construct(...$arguments)
    {
        $this->arguments = $arguments;
    }

    public function find(int $limit = 0, int $page = 0)
    {
        return array();
    }

    public function count(): int
    {
        return 0;
    }
}
