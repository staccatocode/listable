<?php

/*
 * This file is part of staccato listable component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\Listable\Tests\Helper;

use PHPUnit\Framework\TestCase;
use Staccato\Component\Listable\Helper\ArrayCleaner;

/**
 * @covers \Staccato\Component\Listable\Helper\ArrayCleaner
 */
class ArrayCleanerTest extends TestCase
{
    /**
     * @dataProvider cleanDataProvider
     */
    public function testClean(array $input, array $expectedResult): void
    {
        $this->assertSame($expectedResult, ArrayCleaner::clean($input));
    }

    public function cleanDataProvider(): array
    {
        return [
            [
                // input
                ['1' => '    1', '2' => '    2  ', '3' => '3     '],
                // expected result
                ['1' => '1', '2' => '2', '3' => '3'],
            ],
            [
                // input
                ['1' => '    ', '2' => null],
                // expected result
                [],
            ],
            [
                // input
                ['1' => false, '2' => true],
                // expected result
                ['1' => '0', '2' => '1'],
            ],
            [
                // input
                [
                    'a' => [],
                    'b' => '0',
                    'c' => ' c ',
                    'd' => [
                        '1' => '',
                        '2' => ' a ',
                        '3' => [
                            'a' => null,
                            'b' => 0,
                            'c' => ['a' => 'a'],
                        ],
                    ],
                    'e' => [null, 'a' => null],
                ],
                // expected result
                [
                    'b' => '0',
                    'c' => 'c',
                    'd' => [
                        '2' => 'a',
                        '3' => [
                            'b' => '0',
                            'c' => ['a' => 'a'],
                        ],
                    ],
                ],
            ],
        ];
    }
}
