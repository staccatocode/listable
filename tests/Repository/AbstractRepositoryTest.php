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

/**
 * @covers \Staccato\Component\Listable\Repository\AbstractRepository
 */
class AbstractRepositoryTest extends TestCase
{
    protected function setUp()
    {
        $this->repository = $this->getMockForAbstractClass(AbstractRepository::class);
    }

    /**
     * @covers \Staccato\Component\Listable\Repository\AbstractRepository::filterBy
     */
    public function testFilterBy()
    {
        $reflection = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflection->getProperty('filters');
        $reflectionProperty->setAccessible(true);
        
        $this->assertSame(array(), $reflectionProperty->getValue($this->repository));

        $this->repository->filterBy('a', false);
        $this->repository->filterBy('b', 1);
        $this->repository->filterBy('c', '2');

        $this->assertSame(array('a' => false, 'b' => 1, 'c' => '2'), 
            $reflectionProperty->getValue($this->repository));
        
        $this->repository->filterBy('a', true);

        $this->assertSame(array('a' => true, 'b' => 1, 'c' => '2'), 
            $reflectionProperty->getValue($this->repository));
    }

    /**
     * @covers \Staccato\Component\Listable\Repository\AbstractRepository::setFilters
     */
    public function testSetFilters()
    {
        $reflection = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflection->getProperty('filters');
        $reflectionProperty->setAccessible(true);
        
        $this->repository->setFilters(array('a' => false, 'b' => 1));

        $this->assertSame(array('a' => false, 'b' => 1), 
            $reflectionProperty->getValue($this->repository));

        $this->repository->setFilters(array('a' => true, 'b' => 1, 'c' => '2'));

        $this->assertSame(array('a' => true, 'b' => 1, 'c' => '2'), 
            $reflectionProperty->getValue($this->repository));
    }

    /**
     * @covers \Staccato\Component\Listable\Repository\AbstractRepository::orderBy
     */
    public function testOrderBy()
    {
        $reflection = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflection->getProperty('sorter');
        $reflectionProperty->setAccessible(true);
        
        $this->assertSame(array('name' => null, 'type' => AbstractRepository::ORDER_ASC), 
            $reflectionProperty->getValue($this->repository));

        $this->repository->orderBy('test', AbstractRepository::ORDER_DESC);

        $this->assertSame(array('name' => 'test', 'type' => AbstractRepository::ORDER_DESC), 
            $reflectionProperty->getValue($this->repository));
    }
}
