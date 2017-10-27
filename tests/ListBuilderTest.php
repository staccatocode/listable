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
use Staccato\Component\Listable\ListBuilder;
use Staccato\Component\Listable\ListInterface;
use Staccato\Component\Listable\ListObject;
use Staccato\Component\Listable\ListRequestInterface;
use Staccato\Component\Listable\Repository\AbstractRepository;
use Staccato\Component\Listable\Repository\Exception\InvalidRepositoryException;

/**
 * @covers \Staccato\Component\Listable\ListBuilder
 */
class ListBuilderTest extends TestCase
{
    protected function setUp()
    {
        $this->request = $this->getMockBuilder(ListRequestInterface::class)->getMock();
        $this->repository = $this->getMockBuilder(AbstractRepository::class)->getMock();
    }

    /**
     * @covers \Staccato\Component\Listable\ListBuilder::__construct
     */
    public function testCreate()
    {
        $builder = new ListBuilder($this->request);
        $this->assertInstanceOf(ListBuilder::class, $builder);
    }

    /**
     * @covers \Staccato\Component\Listable\ListBuilder::getList
     */
    public function testGetListInvalidRepository()
    {
        $this->expectException(InvalidRepositoryException::class);
        (new ListBuilder($this->request))->getList();
    }

    /**
     * @covers \Staccato\Component\Listable\ListBuilder::getList
     */
    public function testGetList()
    {
        $list = (new ListBuilder($this->request))
            ->setRepository($this->repository)
            ->getList();

        $this->assertInstanceOf(ListInterface::class, $list);
        $this->assertInstanceOf(ListObject::class, $list);
    }

    /**
     * @covers \Staccato\Component\Listable\ListBuilder::mergeOptions
     */
    public function testMergeOptions()
    {
        $requestSorter = array(
            'name' => 'test',
            'type' => AbstractRepository::ORDER_DESC,
        );

        $this->request
            ->method('getSorter')
            ->willReturn($requestSorter);

        $this->request
            ->method('getFilters')
            ->willReturn(array(
                'request_filter' => 'value',
                'static_filter' => 'changed',
                'overridable_filter' => 'overriden',
            ));

        $list = (new ListBuilder($this->request))
            ->setRepository($this->repository)
            ->setFilter('static_filter', 'static', true)
            ->setFilter('overridable_filter', 'overridable')
            ->setSorter('test', AbstractRepository::ORDER_ASC)
            ->getList();

        $options = $list->getOptions();

        $this->assertSame(array(
            'static_filter' => 'static',
            'overridable_filter' => 'overriden',
            'request_filter' => 'value',
        ), $options['filters']);

        $this->assertSame($requestSorter, $options['sorter']);
    }
}
