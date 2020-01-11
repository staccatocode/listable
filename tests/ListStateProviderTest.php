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
use Staccato\Component\Listable\Filter\AbstractFilter;
use Staccato\Component\Listable\Filter\TextFilter;
use Staccato\Component\Listable\ListConfigInterface;
use Staccato\Component\Listable\ListRequest;
use Staccato\Component\Listable\ListRequestInterface;
use Staccato\Component\Listable\ListStateInterface;
use Staccato\Component\Listable\ListStateProvider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @covers \Staccato\Component\Listable\ListStateProvider
 */
class ListStateProviderTest extends TestCase
{
    /**
     * @var MockObject|ListRequestInterface|null
     */
    private $request;

    /**
     * @var MockObject|ListConfigInterface|null
     */
    private $config;

    /**
     * @var MockObject|SessionInterface|null
     */
    private $session;

    public function setUp(): void
    {
        $this->session = $this->getMockBuilder(SessionInterface::class)->getMock();
        $this->request = $this->getMockBuilder(ListRequestInterface::class)->getMock();
        $this->config = $this->getMockBuilder(ListConfigInterface::class)->getMock();
    }

    public function testCreate(): void
    {
        $view = new ListStateProvider();
        $this->assertInstanceOf(ListStateProvider::class, $view);
    }

    public function testGetState(): void
    {
        $filters = [
            'a' => 1,
            'b' => 'x',
            'c' => [1, 2, 3],
        ];

        $filter = $this->getMockBuilder(TextFilter::class)->getMock();
        $filter
            ->method('isValid')
            ->willReturn(true)
        ;

        $this->config
            ->method('getFiltersParam')
            ->willReturn('search')
        ;

        $this->config
            ->method('getFilters')
            ->willReturn([
                'b' => $filter,
            ])
        ;

        $this->request
            ->method('getFilters')
            ->willReturn($filters)
        ;

        $provider = new ListStateProvider($this->request);
        $this->assertInstanceOf(ListStateInterface::class, $provider->getState($this->config));
    }

    public function testGetPersistedState(): void
    {
        $sessionData = [
            'page' => 10,
            'limit' => 15,
            'filters' => [
                'x' => '1',
            ],
            'sorter' => [
                'y' => 'asc',
            ],
        ];

        $filter = $this->getMockBuilder(AbstractFilter::class)->getMock();
        $filter
            ->method('isValid')
            ->willReturn(true)
        ;

        $this->config->method('getPersistState')->willReturn(true);
        $this->config->method('getName')->willReturn('test');
        $this->config->method('getPageParam')->willReturn('page');
        $this->config->method('getLimitParam')->willReturn('limit');
        $this->config->method('getFilters')->willReturn([
            'x' => $filter,
        ]);

        $this->session
            ->method('get')
            ->with('staccato.listable.test.state')
            ->willReturn($sessionData);

        $this->session
            ->expects($this->once())
            ->method('set')
            ->with('staccato.listable.test.state');

        $provider = new ListStateProvider(new ListRequest(), $this->session);
        $state = $provider->getState($this->config);

        $this->assertEquals($sessionData['page'], $state->getPage());
        $this->assertEquals($sessionData['limit'], $state->getLimit());
        $this->assertEquals($sessionData['filters'], $state->getFilters());
        $this->assertEquals($sessionData['sorter'], $state->getSorter());
        $this->assertEquals($sessionData, $state->toArray());
    }
}
