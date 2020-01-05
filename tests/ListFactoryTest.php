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
use Staccato\Component\Listable\ListFactory;
use Staccato\Component\Listable\ListInterface;
use Staccato\Component\Listable\Tests\Fake\Type\FakeListType;

/**
 * @covers \Staccato\Component\Listable\ListFactory
 */
class ListFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new ListFactory();
        $this->assertInstanceOf(ListFactory::class, $factory);
    }

    public function testCreateList(): void
    {
        $factory = new ListFactory();
        $list = $factory->create(FakeListType::class);

        $this->assertInstanceOf(ListInterface::class, $list);
    }
}
