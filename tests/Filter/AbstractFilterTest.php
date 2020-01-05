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

/**
 * @covers \Staccato\Component\Listable\Filter\AbstractFilter
 */
class AbstractFilterTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $filter = new class() extends AbstractFilter {
            public function isValid($value): bool
            {
                return true;
            }
        };

        $this->assertFalse($filter->isVisible());
        $this->assertFalse($filter->isLocked());
        $this->assertFalse($filter->hasField());
        $this->assertNull($filter->getData());
        $this->assertSame('', $filter->getLabel());
        $this->assertSame(array(), $filter->getOptions());

        $type = explode('\\', \get_class($filter));
        $type = array_pop($type);
        $options = array(
            'data' => 'data',
            'visible' => true,
            'locked' => true,
            'label' => 'label',
            'type' => $type,
            'field' => 'test',
        );

        $filter->setOptions($options);

        $this->assertTrue($filter->isVisible());
        $this->assertTrue($filter->isLocked());
        $this->assertSame('data', $filter->getData());
        $this->assertSame('label', $filter->getLabel());
        $this->assertSame('test', $filter->getField());
        $this->assertSame($type, $filter->getType());
        $this->assertEquals($options, $filter->getOptions());
    }
}
