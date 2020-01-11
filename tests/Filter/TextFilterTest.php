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
use Staccato\Component\Listable\Filter\TextFilter;

/**
 * @covers \Staccato\Component\Listable\Filter\TextFilter
 */
class TextFilterTest extends TestCase
{
    public function testCreate(): void
    {
        $filter = new TextFilter();

        $this->assertInstanceOf(AbstractFilter::class, $filter);
    }

    public function testIsValid(): void
    {
        $filter = new TextFilter();
        $this->assertFalse($filter->isValid([]));
        $this->assertFalse($filter->isValid(1));
        $this->assertFalse($filter->isValid(null));
        $this->assertTrue($filter->isValid('test'));
        $this->assertTrue($filter->isValid(''));
    }
}
