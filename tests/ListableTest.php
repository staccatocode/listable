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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Staccato\Component\Listable\Listable;
use Staccato\Component\Listable\ListConfigInterface;
use Staccato\Component\Listable\ListInterface;
use Staccato\Component\Listable\ListStateInterface;
use Staccato\Component\Listable\ListTypeInterface;
use Staccato\Component\Listable\ListView;
use Staccato\Component\Listable\Repository\AbstractRepository;

/**
 * @covers \Staccato\Component\Listable\Listable
 */
class ListableTest extends TestCase
{
    /**
     * @var MockObject|ListConfigInterface|null
     */
    private $config;

    /**
     * @var MockObject|ListStateInterface|null
     */
    private $state;

    /**
     * @var MockObject|AbstractRepository|null
     */
    private $repository;

    protected function setUp(): void
    {
        $this->config = $this->getMockBuilder(ListConfigInterface::class)->getMock();
        $this->state = $this->getMockBuilder(ListStateInterface::class)->getMock();
        $this->repository = $this->getMockBuilder(AbstractRepository::class)->getMock();
    }

    /**
     * @covers \Staccato\Component\Listable\Listable::__construct
     */
    public function testCreate(): void
    {
        $list = new Listable($this->config, $this->state);

        $this->assertInstanceOf(Listable::class, $list);
        $this->assertInstanceOf(ListInterface::class, $list);
    }

    public function testSettersAndGetters(): void
    {
        $list = new Listable($this->config, $this->state);

        $this->state
            ->method('getPage')
            ->willReturn(10)
        ;

        $this->assertSame(0, $list->getPage());
        $list->setPage(10);
        $this->assertSame(10, $list->getPage());
        $this->assertFalse($list->checkPageOverflow());
        $list->setPage(5);
        $this->assertTrue($list->checkPageOverflow());

        $this->assertSame(0, $list->getTotalCount());
        $list->setTotalCount(100);
        $this->assertSame(100, $list->getTotalCount());

        $this->assertSame(0, $list->getTotalPages());
        $list->setTotalPages(3);
        $this->assertSame(3, $list->getTotalPages());

        $this->assertSame($this->state, $list->getState());
        $this->assertSame($this->config, $list->getConfig());
        $this->assertSame([], $list->getData());
        $list->setData([1, 2, 3]);
        $this->assertSame([1, 2, 3], $list->getData());
    }

    /**
     * @covers \Staccato\Component\Listable\Listable::createView
     */
    public function testCreateView(): void
    {
        $mockType = $this->getMockBuilder(ListTypeInterface::class)->getMock();
        $mockType
            ->expects($this->once())
            ->method('buildView')
        ;

        $this->config
            ->method('getPage')
            ->willReturn(5);

        $this->config
            ->method('getType')
            ->willReturn($mockType);

        $list = new Listable($this->config, $this->state);
        $listView = $list->createView();

        $this->assertInstanceOf(ListView::class, $listView);
        $this->assertObjectHasAttribute('vars', $listView);
        $this->assertIsArray($listView->vars);

        $this->assertArrayHasKey('state', $listView->vars);
        $this->assertArrayHasKey('config', $listView->vars);
        $this->assertArrayHasKey('data', $listView->vars);
        $this->assertArrayHasKey('pagination', $listView->vars);
        $this->assertArrayHasKey('count', $listView->vars['pagination']);
        $this->assertArrayHasKey('total', $listView->vars['pagination']);
        $this->assertArrayHasKey('pages', $listView->vars['pagination']);
        $this->assertArrayHasKey('page', $listView->vars['pagination']);
        $this->assertArrayHasKey('limit', $listView->vars['pagination']);
    }
}
