<?php

/*
 * This file is part of staccato listable component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\Listable\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Staccato\Component\Listable\Filter\AbstractFilter;
use Staccato\Component\Listable\Filter\NumberFilter;

/**
 * @covers \Staccato\Component\Listable\Filter\NumberFilter
 */
class NumberFilterTest extends TestCase
{
    public function testCreate(): void
    {
        $filter = new NumberFilter();

        $this->assertInstanceOf(AbstractFilter::class, $filter);
    }

    public function testIsValid(): void
    {
        $filter = new NumberFilter();
        $this->assertFalse($filter->isValid([]));
        $this->assertFalse($filter->isValid(null));
        $this->assertFalse($filter->isValid('test'));
        $this->assertFalse($filter->isValid(''));
        $this->assertFalse($filter->isValid(['from' => 'asd', 'to' => 1]));
        $this->assertFalse($filter->isValid(['from' => 1, 'to' => 'asd']));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid(2));
        $this->assertTrue($filter->isValid(['from' => -0.5, 'to' => 100.5]));
        $this->assertTrue($filter->isValid(['from' => 3]));
        $this->assertTrue($filter->isValid(['to' => 100]));
    }
}
