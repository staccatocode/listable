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
use Staccato\Component\Listable\Exception\InvalidArgumentException;
use Staccato\Component\Listable\Field\TextField;
use Staccato\Component\Listable\Filter\TextFilter;
use Staccato\Component\Listable\Listable;
use Staccato\Component\Listable\ListBuilder;
use Staccato\Component\Listable\ListInterface;
use Staccato\Component\Listable\ListRegistry;
use Staccato\Component\Listable\ListRegistryInterface;
use Staccato\Component\Listable\ListRequestInterface;
use Staccato\Component\Listable\ListState;
use Staccato\Component\Listable\ListStateProviderInterface;
use Staccato\Component\Listable\Repository\AbstractRepository;
use Staccato\Component\Listable\Repository\Result;
use Staccato\Component\Listable\Tests\Fake\Repository\FakeRepository;

/**
 * @covers \Staccato\Component\Listable\ListBuilder
 */
class ListBuilderTest extends TestCase
{
    /**
     * @var MockObject|ListRegistryInterface|null
     */
    private $registry;

    /**
     * @var MockObject|AbstractRepository|null
     */
    private $repository;

    /**
     * @var MockObject|ListStateProviderInterface|null
     */
    private $stateProvider;

    /**
     * @var MockObject|ListRequestInterface|null
     */
    private $request;

    protected function setUp(): void
    {
        $this->registry = $this->getMockBuilder(ListRegistryInterface::class)->getMock();
        $this->request = $this->getMockBuilder(ListRequestInterface::class)->getMock();
        $this->repository = $this->getMockBuilder(AbstractRepository::class)->getMock();
        $this->stateProvider = $this->getMockBuilder(ListStateProviderInterface::class)->getMock();
    }

    /**
     * @covers \Staccato\Component\Listable\ListBuilder::__construct
     */
    public function testCreate(): void
    {
        $builder = new ListBuilder($this->registry);
        $this->assertInstanceOf(ListBuilder::class, $builder);
    }

    public function testAddAndRemoveElements(): void
    {
        $builder = new ListBuilder($this->registry);

        $this->assertFalse($builder->has('a'));

        $builder->add('a', TextField::class);
        $builder->add('b', TextFilter::class);
        $this->assertTrue($builder->has('a'));
        $this->assertTrue($builder->has('b'));

        $builder->remove('a');
        $this->assertFalse($builder->has('a'));
    }

    public function testAddInvalidType(): void
    {
        $this->registry
            ->method('getFieldType')
            ->will($this->throwException(new InvalidArgumentException()))
        ;

        $this->registry
            ->method('getFilterType')
            ->will($this->throwException(new InvalidArgumentException('Not found fake type.')))
        ;

        $builder = new ListBuilder($this->registry);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Could not resolve unsupported element type `invalid_type`.');

        $builder->add('a', 'invalid_type', array('x' => 1));
        $builder->getList();
    }

    /**
     * @covers \Staccato\Component\Listable\ListBuilder::getList
     */
    public function testGetList(): void
    {
        $list = (new ListBuilder($this->registry))->getList();

        $this->assertInstanceOf(ListInterface::class, $list);
        $this->assertInstanceOf(Listable::class, $list);
    }

    /**
     * @covers \Staccato\Component\Listable\ListBuilder::getList
     */
    public function testGetListWithRepository(): void
    {
        $result = new Result();
        $result->setRows(array(1, 2, 3));
        $result->setTotalCount(3);

        $state = new ListState(0, 10);

        $this->registry
            ->method('getRepository')
            ->willReturn($this->repository)
        ;

        $this->stateProvider
            ->method('getState')
            ->willReturn($state)
        ;

        $this->repository
            ->method('getResult')
            ->willReturn($result)
        ;

        $builder = new ListBuilder($this->registry);
        $builder->setRepository(AbstractRepository::class, array('option' => 'test'));
        $list = $builder->getList();

        $this->assertInstanceOf(ListInterface::class, $list);
    }

    /**
     * @covers \Staccato\Component\Listable\ListConfigBuilder::jsonSerialize
     */
    public function testJsonSerialize(): void
    {
        $builder = new ListBuilder($this->registry);
        $builder->setField('test_field', TextField::class);
        $builder->setFilter('test_filter', TextFilter::class);
        $builder->getList();

        $this->assertJson(json_encode($builder));
    }

    public function testPageOverflow(): void
    {
        $data = array(
            array('test_field' => 1),
            array('test_field' => 2),
            array('test_field' => 3),
        );

        $registry = new ListRegistry();
        $builder = new ListBuilder($registry);
        $builder->setRepository(FakeRepository::class, array('data' => $data));
        $builder->add('test_field', TextField::class);
        $builder->add('test_filter', TextFilter::class);
        $builder->setLimit(1);
        $builder->setPage(10);

        $list = $builder->getList();

        $this->assertSame(\count($data), $list->getTotalCount());
        $this->assertSame(2, $list->getPage());
        $this->assertSame(3, $list->getTotalPages());
        $this->assertSame(array(array('test_field' => 3)), $list->getData());
    }
}
