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
use Staccato\Component\Listable\ListRequest;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @covers \Staccato\Component\Listable\ListRequest
 */
class ListRequestTest extends TestCase
{
    protected function setUp()
    {
        $this->request = $this->getMockBuilder(Request::class)->getMock();
        $this->request->query = $this->getMockBuilder(ParameterBag::class)->getMock();
        $this->session = $this->getMockBuilder(Session::class)->getMock();
    }

    /**
     * @covers \Staccato\Component\Listable\ListRequest::__construct
     */
    public function testCreate()
    {
        $listRequest = $this->createListRequest();
        $this->assertInstanceOf(ListRequest::class, $listRequest);
    }

    /**
     * @covers \Staccato\Component\Listable\ListRequest::getPage
     */
    public function testGetPage()
    {
        $listRequest = $this->createListRequest();
        $listRequest->request->query
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
    public function testGetLimit()
    {
        $listRequest = $this->createListRequest();
        $listRequest->request->query
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
    public function testGetFilters()
    {
        $testFilters = array(
            'filter' => 'value',
            'multiple' => array('a', 'b'),
        );

        $listRequest = $this->createListRequest();
        $listRequest->request->query
            ->method('get')
            ->with($this->identicalTo('list'))
            ->will($this->onConsecutiveCalls($testFilters, 'invalid'));

        $listRequest->session
            ->method('get')
            ->with($this->identicalTo('st.list.list'))
            ->will($this->onConsecutiveCalls($testFilters, 'invalid'));

        $this->assertSame($testFilters, $listRequest->getFilters('list', 'get'));
        $this->assertSame(array(), $listRequest->getFilters('list', 'get'));
        $this->assertSame($testFilters, $listRequest->getFilters('list', 'session'));
        $this->assertSame(array(), $listRequest->getFilters('list', 'session'));
    }

    /**
     * @covers \Staccato\Component\Listable\ListRequest::storeFilters
     */
    public function testStoreFilters()
    {
        $testFilters = array(
            'filter' => 1,
        );

        $listRequest = $this->createListRequest();
        $listRequest->session
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->identicalTo('st.list.list'),
                $this->identicalTo($testFilters)
            );

        $listRequest->storeFilters('list', $testFilters);
    }

    /**
     * @covers \Staccato\Component\Listable\ListRequest::getSorter
     */
    public function testGetSorter()
    {
        $listRequest = $this->createListRequest();
        $listRequest->request->query
            ->method('has')
            ->with($this->logicalOr(
                $this->equalTo('asc_true'),
                $this->equalTo('asc_false'),
                $this->equalTo('desc_true'),
                $this->equalTo('desc_false')
            ))
            ->will($this->returnValueMap(array(
                array('asc_true', true),
                array('asc_false', false),
                array('desc_true', true),
                array('desc_false', false),
            )));

        $listRequest->request->query
            ->method('get')
            ->with($this->logicalOr(
                $this->equalTo('asc_true'),
                $this->equalTo('desc_true')
            ))
            ->willReturn('created_at');

        $this->assertSame(array(
            'name' => 'created_at',
            'type' => 'asc',
        ), $listRequest->getSorter('asc_true', 'desc_false'));

        $this->assertSame(array(
            'name' => 'created_at',
            'type' => 'desc',
        ), $listRequest->getSorter('asc_false', 'desc_true'));
    }

    protected function createListRequest()
    {
        $listRequest = new ListRequest();
        $listRequest->request = $this->request;
        $listRequest->session = $this->session;

        return $listRequest;
    }
}
