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
use Staccato\Component\Listable\Field\AbstractField;

/**
 * @covers \Staccato\Component\Listable\Field\AbstractField
 */
class AbstractFieldTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $field = new class() extends AbstractField {
        };

        $this->assertFalse($field->isVisible());
        $this->assertFalse($field->hasFilter());
        $this->assertEmpty($field->getFilterOptions());
        $this->assertNull($field->getPropertyPath());
        $this->assertSame('', $field->getType());

        $type = explode('\\', \get_class($field));
        $type = array_pop($type);
        $options = [
            'property_path' => 'test',
            'visible' => true,
            'type' => $type,
            'filter' => 'test',
            'filter_options' => ['a' => 1],
            'render' => function () {
                return null;
            },
            'normalize' => function () {
                return null;
            },
        ];

        $field->setOptions($options);

        $this->assertTrue($field->isVisible());
        $this->assertSame('test', $field->getPropertyPath());
        $this->assertSame('test', $field->getFilter());
        $this->assertSame(['a' => 1], $field->getFilterOptions());
        $this->assertSame($type, $field->getType());
        $this->assertEquals($options, $field->getOptions());
    }

    public function testNormalizeAndRenderValue()
    {
        $field = new class() extends AbstractField {
        };

        $value = new \DateTime('2020-01-01');

        $field->setOptions([
            'normalize' => function (\DateTime $value) {
                return $value->format('Y-m-d');
            },
            'render' => function (string $value) {
                return $value . '-render';
            },
        ]);

        $this->assertSame('2020-01-01', $field->normalize($value, $this));
        $this->assertSame('2020-01-01-render', $field->render('2020-01-01', $this));
    }
}
