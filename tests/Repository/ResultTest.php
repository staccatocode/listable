<?php

/*
 * This file is part of staccato listable component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\Listable\Tests;

use PHPUnit\Framework\TestCase;
use Staccato\Component\Listable\Repository\Result;

/**
 * @covers \Staccato\Component\Listable\Repository\Result
 */
class ResultTest extends TestCase
{
    public function testCreate(): void
    {
        $result = new Result();

        $this->assertInstanceOf(Result::class, $result);
    }

    public function testGettersAndSetters(): void
    {
        $result = new Result();

        $this->assertSame(0, $result->getTotalCount());
        $result->setTotalCount(10);
        $this->assertSame(10, $result->getTotalCount());

        $this->assertSame(array(), $result->getRows());
        $result->setRows(array(1, 2, 3));
        $this->assertSame(array(1, 2, 3), $result->getRows());
    }
}
