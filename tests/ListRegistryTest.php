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
use Staccato\Component\Listable\Exception\InvalidArgumentException;
use Staccato\Component\Listable\Field\AbstractField;
use Staccato\Component\Listable\Field\TextField;
use Staccato\Component\Listable\Filter\AbstractFilter;
use Staccato\Component\Listable\Filter\TextFilter;
use Staccato\Component\Listable\ListRegistry;
use Staccato\Component\Listable\ListStateProvider;
use Staccato\Component\Listable\ListStateProviderInterface;
use Staccato\Component\Listable\ListTypeInterface;
use Staccato\Component\Listable\Repository\AbstractRepository;
use Staccato\Component\Listable\Tests\Fake\Repository\FakeRepository;
use Staccato\Component\Listable\Tests\Fake\Type\FakeListType;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @covers \Staccato\Component\Listable\ListRegistry
 */
class ListRegistryTest extends TestCase
{
    public function testCreate(): void
    {
        $registry = new ListRegistry();
        $this->assertInstanceOf(ListRegistry::class, $registry);
    }

    public function testGetListType(): void
    {
        $registry = new ListRegistry();
        $this->assertInstanceOf(FakeListType::class, $registry->getListType(FakeListType::class));

        $mockListType = $this->getMockBuilder(FakeListType::class)->getMock();

        $registry = new ListRegistry(array(
            ListTypeInterface::class => new ServiceLocator(array(
                FakeListType::class => static function () use ($mockListType) {
                    return $mockListType;
                }, ),
            ),
        ));

        $this->assertSame($mockListType, $registry->getListType(FakeListType::class));
    }

    public function testGetFieldType(): void
    {
        $registry = new ListRegistry();
        $this->assertInstanceOf(TextField::class, $registry->getFieldType(TextField::class));

        $mockFieldType = $this->getMockBuilder(TextField::class)->getMock();

        $registry = new ListRegistry(array(
            AbstractField::class => new ServiceLocator(array(
                TextField::class => static function () use ($mockFieldType) {
                    return $mockFieldType;
                }, ),
            ),
        ));

        $this->assertSame($mockFieldType, $registry->getFieldType(TextField::class));
    }

    public function testGetFilterType(): void
    {
        $registry = new ListRegistry();
        $this->assertInstanceOf(TextFilter::class, $registry->getFilterType(TextFilter::class));

        $mockFilterType = $this->getMockBuilder(TextFilter::class)->getMock();

        $registry = new ListRegistry(array(
            AbstractFilter::class => new ServiceLocator(array(
                TextFilter::class => static function () use ($mockFilterType) {
                    return $mockFilterType;
                }, ),
            ),
        ));

        $this->assertSame($mockFilterType, $registry->getFilterType(TextFilter::class));
    }

    public function testGetStateProvider(): void
    {
        $registry = new ListRegistry();
        $this->assertInstanceOf(ListStateProvider::class, $registry->getStateProvider(ListStateProvider::class));

        $mockStateProvider = $this->getMockBuilder(ListStateProvider::class)->getMock();

        $registry = new ListRegistry(array(
            ListStateProviderInterface::class => new ServiceLocator(array(
                ListStateProvider::class => static function () use ($mockStateProvider) {
                    return $mockStateProvider;
                }, ),
            ),
        ));

        $this->assertSame($mockStateProvider, $registry->getStateProvider(ListStateProvider::class));
    }

    public function testGetRepository(): void
    {
        $registry = new ListRegistry();
        $this->assertInstanceOf(FakeRepository::class, $registry->getRepository(FakeRepository::class));

        $mockRepositoryOptions = array('a' => 1, 'b' => false);
        $mockRepository = $this->getMockBuilder(AbstractRepository::class)->getMock();
        $mockRepository
            ->method('setOptions')
            ->with($mockRepositoryOptions)
            ->willReturnSelf()
        ;

        $registry = new ListRegistry(array(
            AbstractRepository::class => new ServiceLocator(array(
                AbstractRepository::class => static function () use ($mockRepository) {
                    return $mockRepository;
                }, ),
            ),
        ));

        $this->assertSame($mockRepository, $registry->getRepository(AbstractRepository::class, $mockRepositoryOptions));
    }

    public function testGetWithInvalidArgument(): void
    {
        $registry = new ListRegistry();

        $this->expectException(InvalidArgumentException::class);

        $registry->getFieldType('invalid');
    }
}
