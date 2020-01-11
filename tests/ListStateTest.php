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
use Staccato\Component\Listable\ListState;

/**
 * @covers \Staccato\Component\Listable\ListState
 */
class ListStateTest extends TestCase
{
    public function testCreate(): void
    {
        $state = new ListState(1, 2);
        $this->assertInstanceOf(ListState::class, $state);

        $this->assertSame(1, $state->getPage());
        $this->assertSame(2, $state->getLimit());
    }

    public function testGettersAndSetters(): void
    {
        $state = new ListState(0, 0);

        $state->setPage(10);
        $this->assertSame(10, $state->getPage());

        $state->setLimit(20);
        $this->assertSame(20, $state->getLimit());

        $this->assertSame([], $state->getSorter());
        $state->setSorter(['a' => 1, 'b' => 2]);
        $this->assertSame(['a' => 1, 'b' => 2], $state->getSorter());

        $this->assertSame([], $state->getFilters());
        $state->setFilters(['a' => 1]);
        $this->assertSame(['a' => 1], $state->getFilters());

        $data = [
            'page' => 99,
            'limit' => 99,
            'filters' => ['y' => 1],
            'sorter' => ['x' => 'asc'],
        ];

        $state->fromArray($data);

        $this->assertEquals($data['filters'], $state->getFilters());
        $this->assertEquals($data['sorter'], $state->getSorter());
        $this->assertEquals($data['page'], $state->getPage());
        $this->assertEquals($data['limit'], $state->getLimit());
        $this->assertEquals($data, $state->toArray());
    }

    public function testJsonSerialize(): void
    {
        $state = new ListState(0, 0);

        $this->assertJson(json_encode($state));
    }
}
