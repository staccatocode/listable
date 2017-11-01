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
use Staccato\Component\Listable\Repository\AbstractRepository;
use Staccato\Component\Listable\Repository\Exception\InvalidRepositoryFactoryException;
use Staccato\Component\Listable\Repository\RepositoryFactory;
use Staccato\Component\Listable\Repository\RepositoryFactoryInterface;

/**
 * @covers \Staccato\Component\Listable\Repository\RepositoryFactory
 */
class RepositoryFactoryTest extends TestCase
{
    public function testCreate()
    {
        $repositoryFactory = new RepositoryFactory();

        $this->assertInstanceOf(RepositoryFactory::class, $repositoryFactory);
    }

    /**
     * @covers \Staccato\Component\Listable\Repository\RepositoryFactory::create
     * @covers \Staccato\Component\Listable\Repository\RepositoryFactory::add
     * @covers \Staccato\Component\Listable\Repository\RepositoryFactory::has
     * @covers \Staccato\Component\Listable\Repository\RepositoryFactory::remove
     */
    public function testRepositoryFactory()
    {
        $repositoryFactory = new RepositoryFactory();

        $mockRepositoryFactory = $this->getMockBuilder(RepositoryFactoryInterface::class)->getMock();
        $mockRepository = $this->getMockBuilder(AbstractRepository::class)->getMock();

        $this->assertFalse($repositoryFactory->has('mock'));
        $repositoryFactory->add('mock', $mockRepositoryFactory);
        $this->assertTrue($repositoryFactory->has('mock'));

        $mockRepositoryFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->identicalTo('test_argument'))
            ->willReturn($mockRepository);

        $this->assertSame($repositoryFactory->create('mock', 'test_argument'), $mockRepository);
        $repositoryFactory->remove('mock');
        $this->assertFalse($repositoryFactory->has('mock'));
    }

    public function testCreateInvalidRepositoryFactoryException()
    {
        $repositoryFactory = new RepositoryFactory();

        $this->expectException(InvalidRepositoryFactoryException::class);
        $repositoryFactory->create('unknow');
    }
}
