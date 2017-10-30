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
use Staccato\Component\Listable\ListConfigInterface;
use Staccato\Component\Listable\ListInterface;
use Staccato\Component\Listable\ListObject;
use Staccato\Component\Listable\ListView;
use Staccato\Component\Listable\Repository\AbstractRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \Staccato\Component\Listable\ListObject
 */
class ListObjectTest extends TestCase
{
    protected function setUp()
    {
        $this->config = $this->getMockBuilder(ListConfigInterface::class)->getMock();
        $this->repository = $this->getMockBuilder(AbstractRepository::class)->getMock();
    }

    /**
     * @covers \Staccato\Component\Listable\ListObject::__construct
     */
    public function testCreate()
    {
        $list = new ListObject($this->config);

        $this->assertInstanceOf(ListObject::class, $list);
        $this->assertInstanceOf(ListInterface::class, $list);
    }

    /**
     * @covers \Staccato\Component\Listable\ListObject::getData
     * @covers \Staccato\Component\Listable\ListObject::getPage
     * @covers \Staccato\Component\Listable\ListObject::count
     * @covers \Staccato\Component\Listable\ListObject::countPages
     * @covers \Staccato\Component\Listable\ListObject::getLimit
     * @covers \Staccato\Component\Listable\ListObject::getLimitParam
     * @covers \Staccato\Component\Listable\ListObject::getName
     * @covers \Staccato\Component\Listable\ListObject::getPageParam
     * @covers \Staccato\Component\Listable\ListObject::getFilterSource
     * @covers \Staccato\Component\Listable\ListObject::getSorterParams
     * @covers \Staccato\Component\Listable\ListObject::getOptions
     * @covers \Staccato\Component\Listable\ListObject::getRepository
     */
    public function testGetters()
    {
        $list = new ListObject($this->config);

        $this->assertSame(0, $list->getPage());
        $this->assertSame(0, $list->count());
        $this->assertSame(0, $list->countPages());
        $this->assertSame(0, $list->getLimit());
        $this->assertSame('', $list->getName());
        $this->assertSame('', $list->getPageParam());
        $this->assertSame('', $list->getLimitParam());
        $this->assertSame(array(), $list->getData());

        $this->config
            ->method('getLimit')
            ->willReturn(10);

        $this->assertSame(10, $list->getLimit());

        $this->config
            ->method('getName')
            ->willReturn('test');

        $this->assertSame('test', $list->getName());

        $this->config
            ->method('getPageParam')
            ->willReturn('test_page');

        $this->assertSame('test_page', $list->getPageParam());

        $this->config
            ->method('getLimitParam')
            ->willReturn('test_limit');

        $this->assertSame('test_limit', $list->getLimitParam());

        $this->config
            ->method('getFilterSource')
            ->willReturn('get');

        $this->assertSame('get', $list->getFilterSource());

        $testSorterParams = array(
            'asc' => 'asc',
            'desc' => 'desc',
        );

        $this->config
            ->method('getSorterParams')
            ->willReturn($testSorterParams);

        $this->assertSame($testSorterParams, $list->getSorterParams());

        $testOptions = array(
            'filter' => array('a' => 1, 'b' => 2),
            'sorter' => array('name' => 'test', 'asc'),
        );

        $this->config
            ->method('getOptions')
            ->willReturn($testOptions);

        $this->assertSame($testOptions, $list->getOptions());

        $this->config
            ->method('getRepository')
            ->willReturn($this->repository);

        $this->assertInstanceOf(AbstractRepository::class, $list->getRepository());
    }

    /**
     * @covers \Staccato\Component\Listable\ListObject::getRepository
     */
    public function testCreateView()
    {
        $this->config
            ->method('getPage')
            ->willReturn(5);

        $list = new ListObject($this->config);
        $listView = $list->createView();

        $this->assertInstanceOf(ListView::class, $listView);
        $this->assertObjectHasAttribute('vars', $listView);
        $this->assertInternalType('array', $listView->vars);
    }

    /**
     * @covers \Staccato\Component\Listable\ListObject::on
     */
    public function testOnHandler()
    {
        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->request = $this->getMockBuilder(ParameterBag::class)->getMock();
        $response = $this->getMockBuilder(Response::class)->getMock();

        $list = new ListObject($this->config);

        $reflection = new \ReflectionClass($list);
        $reflectionProperty = $reflection->getProperty('request');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($list, $request);

        $this->config
            ->method('getName')
            ->willReturn('test');

        $request
            ->method('isMethod')
            ->with($this->identicalTo('post'))
            ->willReturn(true);

        $request->request
            ->method('has')
            ->with($this->identicalTo('st_list'))
            ->willReturn(true);

        $testPostData = array(
            'action' => 'test',
            'name' => 'test',
            'objects' => array('1', '2'),
        );

        $request->request
            ->method('get')
            ->with($this->identicalTo('st_list'))
            ->willReturn($testPostData);

        $response
            ->expects($this->once())
            ->method('send');

        $result = array(array('list' => null, 'data' => null, 'request' => null));

        $list->on('test', function (ListInterface $list, array $data, Request $request) use (&$response, &$result) {
            $result['list'] = $list;
            $result['data'] = $data;
            $result['request'] = $request;

            return $response;
        });

        $this->assertSame($list, $result['list']);
        $this->assertSame($testPostData, $result['data']);
        $this->assertSame($request, $result['request']);
    }

    /**
     * @covers \Staccato\Component\Listable\ListObject::load
     */
    public function testLoad()
    {
        $this->config
            ->method('getRepository')
            ->willReturn($this->repository);

        $this->config
            ->method('getOptions')
            ->willReturn(array(
                'filters' => array(),
                'sorter' => array(
                    'name' => null,
                    'type' => 'asc',
                ),
            ));

        $this->config
            ->method('getLimit')
            ->willReturn(4);

        $this->config
            ->method('getPage')
            ->will($this->onConsecutiveCalls(1, 10, 100));

        $this->repository
            ->method('count')
            ->will($this->onConsecutiveCalls(8, 0, 8));

        $this->repository
            ->method('find')
            ->will($this->returnCallback(function (int $limit, int $page) {
                return range($page * $limit, ($page * $limit) + $limit - 1);
            }));

        $list = new ListObject($this->config);

        $list->load();
        $this->assertSame(range(4, 7), $list->getData());
        $this->assertSame(8, $list->count());
        $this->assertSame(2, $list->countPages());
        $this->assertSame(1, $list->getPage());

        $list->load(); // test empty repo
        $this->assertSame(0, $list->count());
        $this->assertSame(0, $list->countPages());
        $this->assertSame(0, $list->getPage());

        $list->load(); // test page overflow
        $this->assertSame(range(4, 7), $list->getData());
        $this->assertSame(8, $list->count());
        $this->assertSame(2, $list->countPages());
        $this->assertSame(1, $list->getPage());
    }
}
