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
use Staccato\Component\Listable\ListStateInterface;
use Staccato\Component\Listable\Repository\AbstractRepository;
use Staccato\Component\Listable\Repository\ArrayRepository;

/**
 * @covers \Staccato\Component\Listable\Repository\ArrayRepository
 */
class ArrayRepositoryTest extends TestCase
{
    /**
     * @var MockObject|ListStateInterface|null
     */
    private $state;

    public function setUp(): void
    {
        $this->state = $this->getMockBuilder(ListStateInterface::class)->getMock();
    }

    public function testCreate(): void
    {
        $repository = new ArrayRepository();
        $repository->setOptions(array(
            'data' => array(),
        ));

        $this->assertInstanceOf(AbstractRepository::class, $repository);
    }

    public function testGetResult(): void
    {
        $data = $this->prepareTestData(100);

        $this->state
            ->method('getPage')
            ->willReturn(0)
        ;

        $this->state
            ->method('getLimit')
            ->willReturn(1)
        ;

        $repository = new ArrayRepository();
        $repository->setOptions(array(
            'data' => $data,
        ));

        $result = $repository->getResult($this->state);

        $this->assertEquals(100, $result->getTotalCount());
        $this->assertSame(array(array('a' => 'Test 0', 'b' => 1)), $result->getRows());
    }

    public function testFilterResult(): void
    {
        $data = $this->prepareTestData(100);

        $this->state
            ->method('getPage')
            ->willReturn(0)
        ;

        $this->state
            ->method('getLimit')
            ->willReturn(1)
        ;

        $this->state
            ->method('getFilters')
            ->willReturn(array('a' => 'test 9', 'b' => '100'))
        ;

        $repository = new ArrayRepository();
        $repository->setOptions(array(
            'data' => $data,
        ));

        $result = $repository->getResult($this->state);

        $this->assertEquals(1, $result->getTotalCount());
        $this->assertSame(array(array('a' => 'Test 99', 'b' => 100)), $result->getRows());

        $repository->setOptions(array(
            'data' => $data,
            'filter' => static function (array &$rows, ListStateInterface $state) {
                return \array_slice($rows, 0, 50);
            },
        ));

        $result = $repository->getResult($this->state);

        $this->assertEquals(50, $result->getTotalCount());
        $this->assertSame(\array_slice($data, 0, 1), $result->getRows());
    }

    public function testSortResult(): void
    {
        $data = $this->prepareTestData(100);

        $this->state
            ->method('getPage')
            ->willReturn(0)
        ;

        $this->state
            ->method('getLimit')
            ->willReturn(1)
        ;

        $this->state
            ->method('getSorter')
            ->willReturn(array('a' => 'Desc'))
        ;

        $repository = new ArrayRepository();
        $repository->setOptions(array(
            'data' => $data,
        ));

        $result = $repository->getResult($this->state);

        $this->assertEquals(100, $result->getTotalCount());
        $this->assertSame(array(array('a' => 'Test 99', 'b' => 100)), $result->getRows());

        $repository->setOptions(array(
            'data' => $data,
            'sort' => static function (array &$rows, ListStateInterface $state) {
                return $rows;
            },
        ));

        $result = $repository->getResult($this->state);

        $this->assertEquals(100, $result->getTotalCount());
        $this->assertSame(array(array('a' => 'Test 0', 'b' => 1)), $result->getRows());
    }

    private function prepareTestData(int $length = 100): array
    {
        $data = array();
        for ($i = 0; $i < $length; ++$i) {
            $data[$i] = array(
                'a' => 'Test ' . $i,
                'b' => $i + 1,
            );
        }

        return $data;
    }
}
