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
use Staccato\Component\Listable\Exception\ClassNotFoundException;
use Staccato\Component\Listable\ListConfigBuilder;
use Staccato\Component\Listable\ListConfigBuilderInterface;
use Staccato\Component\Listable\Repository\AbstractRepository;
use Staccato\Component\Listable\Repository\Exception\InvalidRepositoryException;
use Staccato\Component\Listable\Tests\Repository\FakeRepository;

/**
 * @covers \Staccato\Component\Listable\ListConfigBuilder
 */
class ListConfigBuilderTest extends TestCase
{
    public function testCreate()
    {
        $builder = new ListConfigBuilder();

        $this->assertInstanceOf(ListConfigBuilder::class, $builder);
        $this->assertInstanceOf(ListConfigBuilderInterface::class, $builder);
    }

    /**
     * @covers \Staccato\Component\Listable\ListConfigBuilder::setRepository
     */
    public function testSetRepositoryObject()
    {
        $repository = $this->getMockBuilder(AbstractRepository::class)->getMock();

        $builder = new ListConfigBuilder();
        $builder->setRepository($repository);

        $this->assertInstanceOf(AbstractRepository::class, $builder->getRepository());
        $this->assertSame($repository, $builder->getRepository());
    }

    /**
     * @covers \Staccato\Component\Listable\ListConfigBuilder::setRepository
     */
    public function testSetRepositoryClass()
    {
        $builder = new ListConfigBuilder();

        $builder->setRepository(FakeRepository::class);
        $this->assertInstanceOf(AbstractRepository::class, $builder->getRepository());

        $arguments = array(1, 2, 3);

        $builder->setRepository(FakeRepository::class, $arguments);
        $this->assertInstanceOf(FakeRepository::class, $builder->getRepository());
        $this->assertSame($arguments, $builder->getRepository()->arguments);
    }

    /**
     * @covers \Staccato\Component\Listable\ListConfigBuilder::setRepository
     */
    public function testSetRepositoryNonExistantClass()
    {
        $builder = new ListConfigBuilder();

        $this->expectException(ClassNotFoundException::class);
        $builder->setRepository(NonExistantClass::class);
    }

    /**
     * @covers \Staccato\Component\Listable\ListConfigBuilder::setRepository
     */
    public function testSetRepositoryInvalidObject()
    {
        $builder = new ListConfigBuilder();

        $this->expectException(InvalidRepositoryException::class);
        $builder->setRepository(new \stdClass());
    }

    /**
     * @covers \Staccato\Component\Listable\ListConfigBuilder::setName
     * @covers \Staccato\Component\Listable\ListConfigBuilder::getName
     * @covers \Staccato\Component\Listable\ListConfigBuilder::setFilterSource
     * @covers \Staccato\Component\Listable\ListConfigBuilder::setPage
     * @covers \Staccato\Component\Listable\ListConfigBuilder::getPage
     * @covers \Staccato\Component\Listable\ListConfigBuilder::setPageParam
     * @covers \Staccato\Component\Listable\ListConfigBuilder::getPageParam
     * @covers \Staccato\Component\Listable\ListConfigBuilder::setLimit
     * @covers \Staccato\Component\Listable\ListConfigBuilder::getLimit
     * @covers \Staccato\Component\Listable\ListConfigBuilder::getSorterParams
     * @covers \Staccato\Component\Listable\ListConfigBuilder::setSorter
     * @covers \Staccato\Component\Listable\ListConfigBuilder::setFilter
     * @covers \Staccato\Component\Listable\ListConfigBuilder::setFilters
     */
    public function testGettersAndSetters()
    {
        $builder = new ListConfigBuilder();

        $this->assertNull($builder->getRepository());

        $this->assertSame('list', $builder->getName());
        $builder->setName('test');
        $this->assertSame('test', $builder->getName());

        $this->assertSame('get', $builder->getFilterSource());
        $builder->setFilterSource('session');
        $this->assertSame('session', $builder->getFilterSource());

        $this->assertNull($builder->getPage());
        $builder->setPage(2);
        $this->assertSame(2, $builder->getPage());

        $this->assertSame('page', $builder->getPageParam());
        $builder->setPageParam('test_page');
        $this->assertSame('test_page', $builder->getPageParam());

        $this->assertSame(0, $builder->getLimit());
        $builder->setLimit(15);
        $this->assertSame(15, $builder->getLimit());

        $this->assertSame(array(
            'asc' => AbstractRepository::ORDER_ASC,
            'desc' => AbstractRepository::ORDER_DESC,
        ), $builder->getSorterParams());

        $builder->setSorterParams('test_asc', 'test_desc');
        $this->assertSame(array(
            'asc' => 'test_asc',
            'desc' => 'test_desc',
        ), $builder->getSorterParams());

        $builder->setSorter('test', 'desc');
        $builder->setFilter('test_filter', 'value');
        $builder->setFilters(array('other_filters' => 1));

        $options = $builder->getOptions();

        $this->assertSame(array(
            'name' => 'test',
            'type' => 'desc',
        ), $options['sorter']);

        $this->assertSame(array(
            'test_filter' => 'value',
            'other_filters' => 1,
        ), $options['filters']);

        $this->assertEquals($builder, $builder->getListConfig());
    }
}
