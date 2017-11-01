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
use Staccato\Component\Listable\Repository\AbstractRepository;
use Staccato\Component\Listable\Repository\ClassRepositoryFactory;
use Staccato\Component\Listable\Repository\RepositoryFactoryInterface;
use Staccato\Component\Listable\Tests\Repository\FakeRepository;

/**
 * @covers \Staccato\Component\Listable\Repository\ClassRepositoryFactory
 */
class ClassRepositoryFactoryTest extends TestCase
{
    public function testCreate()
    {
        $repositoryFactory = new ClassRepositoryFactory();

        $this->assertInstanceOf(ClassRepositoryFactory::class, $repositoryFactory);
        $this->assertInstanceOf(RepositoryFactoryInterface::class, $repositoryFactory);
    }

    /**
     * @covers \Staccato\Component\Listable\Repository\ClassRepositoryFactory::create
     */
    public function testClassRepositoryFactory()
    {
        $repositoryFactory = new ClassRepositoryFactory();
        $repository = $repositoryFactory->create(FakeRepository::class);
        $this->assertInstanceof(AbstractRepository::class, $repository);

        $repository = $repositoryFactory->create(array(FakeRepository::class, 'argument'));
        $this->assertInstanceof(AbstractRepository::class, $repository);
        $this->assertSame(array('argument'), $repository->arguments);
    }

    /**
     * @covers \Staccato\Component\Listable\Repository\ClassRepositoryFactory::create
     */
    public function testClassRepositoryFactoryClassNotFoundException()
    {
        $repositoryFactory = new ClassRepositoryFactory();

        $this->expectException(ClassNotFoundException::class);
        $repositoryFactory->create('NonExistantClass');
    }
}
