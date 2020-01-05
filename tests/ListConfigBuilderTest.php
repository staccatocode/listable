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
use Staccato\Component\Listable\Field\AbstractField;
use Staccato\Component\Listable\Filter\AbstractFilter;
use Staccato\Component\Listable\ListConfigBuilder;
use Staccato\Component\Listable\ListConfigBuilderInterface;
use Staccato\Component\Listable\ListRegistryInterface;
use Staccato\Component\Listable\ListStateProvider;
use Staccato\Component\Listable\ListTypeInterface;
use Staccato\Component\Listable\Repository\AbstractRepository;

/**
 * @covers \Staccato\Component\Listable\ListConfigBuilder
 */
class ListConfigBuilderTest extends TestCase
{
    /**
     * @var MockObject|ListRegistryInterface|null
     */
    private $registry;

    protected function setUp(): void
    {
        $this->registry = $this->getMockBuilder(ListRegistryInterface::class)->getMock();
    }

    public function testCreate(): void
    {
        $builder = new ListConfigBuilder($this->registry);

        $this->assertInstanceOf(ListConfigBuilder::class, $builder);
        $this->assertInstanceOf(ListConfigBuilderInterface::class, $builder);
    }

    public function testGettersAndSetters(): void
    {
        $builder = new ListConfigBuilder($this->registry);

        $this->assertSame(ListStateProvider::class, $builder->getStateProvider());
        $builder->setStateProvider('test');
        $this->assertSame('test', $builder->getStateProvider());

        $this->assertSame('list', $builder->getName());
        $builder->setName('test');
        $this->assertSame('test', $builder->getName());

        $this->assertSame(0, $builder->getPage());
        $builder->setPage(2);
        $this->assertSame(2, $builder->getPage());

        $this->assertSame('', $builder->getPageParam());
        $builder->setPageParam('test_page');
        $this->assertSame('test_page', $builder->getPageParam());

        $this->assertSame('', $builder->getLimitParam());
        $builder->setLimitParam('test_limit');
        $this->assertSame('test_limit', $builder->getLimitParam());
        $this->assertEquals(array('min' => null, 'max' => null), $builder->getLimitParamOptions());

        $this->assertSame(0, $builder->getLimit());
        $builder->setLimit(15);
        $this->assertSame(15, $builder->getLimit());

        $builder->setLimitParam('test_limit', array('min' => 5, 'max' => 10));
        $this->assertSame(10, $builder->getLimit());
        $this->assertEquals(array('min' => 5, 'max' => 10), $builder->getLimitParamOptions());
        $builder->setLimit(0);
        $this->assertSame(5, $builder->getLimit());

        $this->assertSame(array(), $builder->getSorter());
        $builder->setSorter('test', 'desc');
        $builder->setSorter('name', 'asc');
        $this->assertSame(array('test' => 'desc', 'name' => 'asc'), $builder->getSorter());

        $builder->setSorter('test', null);
        $this->assertSame(array('name' => 'asc'), $builder->getSorter());

        $builder->setSorter(null, null);
        $this->assertSame(array(), $builder->getSorter());

        $this->assertSame('', $builder->getSorterParam());
        $builder->setSorterParam('test_sorter');
        $this->assertSame('test_sorter', $builder->getSorterParam());

        $this->assertSame('', $builder->getFiltersParam());
        $builder->setFiltersParam('test_filters');
        $this->assertSame('test_filters', $builder->getFiltersParam());

        $this->assertFalse($builder->getPersistState());
        $builder->setPersistState(true);
        $this->assertTrue($builder->getPersistState());

        $this->assertEmpty($builder->getOptions());
        $builder->setOptions(array('a' => 1));
        $this->assertSame(array('a' => 1), $builder->getOptions());

        $type = $this->getMockBuilder(ListTypeInterface::class)->getMock();
        $this->assertNull($builder->getType());
        $builder->setType($type);
        $this->assertSame($type, $builder->getType());

        $this->assertEquals($builder, $builder->getListConfig());
    }

    public function testSetRepository(): void
    {
        $builder = new ListConfigBuilder($this->registry);

        $mockRepositoryOptions = array('a' => 1, 'b' => false);
        $mockRepository = $this->getMockBuilder(AbstractRepository::class)->getMock();
        $mockRepository->method('setOptions')
            ->with($mockRepositoryOptions)
            ->willReturnSelf()
        ;

        $this->registry
            ->method('getRepository')
            ->with(AbstractRepository::class)
            ->willReturn($mockRepository)
        ;

        $this->assertNull($builder->getRepository());
        $builder->setRepository(AbstractRepository::class, $mockRepositoryOptions);
        $this->assertSame($mockRepository, $builder->getRepository());
    }

    public function testSetFilter(): void
    {
        $builder = new ListConfigBuilder($this->registry);

        $filterOptions = array('a' => 1, 'b' => true, 'c' => 'value');

        $mockFilter = $this->getMockBuilder(AbstractFilter::class)->getMock();
        $mockFilter
            ->method('setOptions')
            ->with($this->identicalTo($filterOptions))
            ->willReturnSelf()
        ;

        $this->registry
            ->method('getFilterType')
            ->with($this->identicalTo('mockFilter'))
            ->willReturn($mockFilter)
        ;

        $this->assertSame(array(), $builder->getFilters());
        $builder->setFilter('test', 'mockFilter', $filterOptions);
        $this->assertSame(array('test' => $mockFilter), $builder->getFilters());
    }

    public function testSetField(): void
    {
        $builder = new ListConfigBuilder($this->registry);

        $fieldOptions = array('a' => 1, 'b' => true, 'c' => 'value', 'filter' => 'test_filter');

        $mockField = $this->getMockBuilder(AbstractField::class)->getMock();
        $mockField
            ->method('setOptions')
            ->with($this->identicalTo($fieldOptions))
            ->willReturnSelf()
        ;

        $mockField
            ->method('hasFilter')
            ->willReturn(true)
        ;

        $mockField
            ->method('getFilter')
            ->willReturn($fieldOptions['filter'])
        ;

        $this->registry
            ->method('getFieldType')
            ->with($this->identicalTo('mockField'))
            ->willReturn($mockField)
        ;

        $mockFilter = $this->getMockBuilder(AbstractFilter::class)->getMock();

        $this->registry
            ->method('getFilterType')
            ->with($this->identicalTo('test_filter'))
            ->willReturn($mockFilter)
        ;

        $this->assertSame(array(), $builder->getFields());
        $builder->setField('test', 'mockField', $fieldOptions);
        $this->assertSame(array('test' => $mockField), $builder->getFields());
    }
}
