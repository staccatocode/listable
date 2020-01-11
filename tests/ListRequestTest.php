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
use Staccato\Component\Listable\ListRequest;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \Staccato\Component\Listable\ListRequest
 */
class ListRequestTest extends TestCase
{
    /**
     * @var MockObject|Request|null
     */
    private $request;

    protected function setUp(): void
    {
        $this->request = $this->getMockBuilder(Request::class)->getMock();
        $this->request->query = $this->getMockBuilder(ParameterBag::class)->getMock();
    }

    /**
     * @covers \Staccato\Component\Listable\ListRequest::__construct
     */
    public function testCreate(): void
    {
        $listRequest = $this->createListRequest();
        $this->assertInstanceOf(ListRequest::class, $listRequest);
    }

    /**
     * @covers \Staccato\Component\Listable\ListRequest::getPage
     */
    public function testGetPage(): void
    {
        $listRequest = $this->createListRequest();

        $this->getPrivateProperty($listRequest, 'request')->query
            ->method('getInt')
            ->with($this->logicalOr(
                $this->equalTo('page'),
                $this->equalTo('')
            ))
            ->will($this->onConsecutiveCalls(5, -1, 5));

        $this->assertSame(5, $listRequest->getPage('page'));
        $this->assertSame(0, $listRequest->getPage('page'));
        $this->assertSame(0, $listRequest->getPage(''));
    }

    /**
     * @covers \Staccato\Component\Listable\ListRequest::getLimit
     */
    public function testGetLimit(): void
    {
        $listRequest = $this->createListRequest();

        $this->getPrivateProperty($listRequest, 'request')->query
            ->method('getInt')
            ->with($this->logicalOr(
                $this->equalTo('limit'),
                $this->equalTo('')
            ))
            ->will($this->onConsecutiveCalls(5, -1, 5));

        $this->assertSame(5, $listRequest->getLimit('limit'));
        $this->assertSame(0, $listRequest->getLimit('limit'));
        $this->assertSame(0, $listRequest->getLimit(''));
    }

    /**
     * @covers \Staccato\Component\Listable\ListRequest::getFilters
     */
    public function testGetFilters(): void
    {
        $testFilters = [
            'filter' => 'value',
            'multiple' => ['a', 'b'],
        ];

        $listRequest = $this->createListRequest();

        $this->getPrivateProperty($listRequest, 'request')
            ->method('get')
            ->with($this->identicalTo('list'))
            ->will($this->onConsecutiveCalls($testFilters, 'invalid'));

        $this->assertSame($testFilters, $listRequest->getFilters('list'));
        $this->assertSame([], $listRequest->getFilters('list'));
    }

    /**
     * @covers \Staccato\Component\Listable\ListRequest::getSorter
     */
    public function testGetSorter(): void
    {
        $testSorter = [
            'name' => 'asc',
            'created' => 'desc',
        ];

        $testInvalidSorter = [
            'invalid' => ['asc'],
        ];

        $listRequest = $this->createListRequest();

        $this->getPrivateProperty($listRequest, 'request')->query
            ->method('get')
            ->with($this->identicalTo('order'))
            ->will($this->onConsecutiveCalls($testSorter + $testInvalidSorter, 'invalid'));

        $this->assertSame($testSorter, $listRequest->getSorter('order'));
        $this->assertSame([], $listRequest->getSorter('order'));
    }

    protected function createListRequest(): ListRequest
    {
        $listRequest = new ListRequest();

        $this->setPrivateProperty($listRequest, 'request', $this->request);

        return $listRequest;
    }

    protected function setPrivateProperty($object, string $property, $value): void
    {
        $reflection = new \ReflectionClass($object);
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }

    protected function getPrivateProperty($object, string $property)
    {
        $reflection = new \ReflectionClass($object);
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }
}
