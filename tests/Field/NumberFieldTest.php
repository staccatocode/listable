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
use Staccato\Component\Listable\Field\NumberField;

/**
 * @covers \Staccato\Component\Listable\Field\NumberField
 */
class NumberFieldTest extends TestCase
{
    public function testNormalize(): void
    {
        $field = $this->createNumberField();

        $this->assertSame(0.5, $field->normalize(0.5, ['value' => 0.5]));
        $this->assertSame(1.0, $field->normalize(1, ['value' => 1]));
        $this->assertSame(1.5, $field->normalize('1.50', ['value' => '1.50']));
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(float $value, string $expectedResult, array $options): void
    {
        $field = $this->createNumberField($options);

        $this->assertSame($expectedResult, $field->render($value, ['value' => $value]));
    }

    public function renderDataProvider(): array
    {
        return [
            [1.23456, '1.23', []],
            [1000.00, '1000.00', []],
            [1, '1', ['precision' => 0]],
            [1, '1.0', ['precision' => 1]],
            [1.23456, '1.2', ['precision' => 1]],
            [1.65432, '1.7', ['precision' => 1]],
            [1.65432, '1,654', ['precision' => 3, 'decimal_point' => ',']],
            [1234567.89, '1 234 567,890', ['precision' => 3, 'decimal_point' => ',', 'thousands_separator' => ' ']],
        ];
    }

    private function createNumberField(array $options = []): NumberField
    {
        $field = new NumberField();
        $field->setOptions($options);

        return $field;
    }
}
