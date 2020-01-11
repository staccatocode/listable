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
use Staccato\Component\Listable\ListView;

/**
 * @covers \Staccato\Component\Listable\ListView
 */
class ListViewTest extends TestCase
{
    public function testCreate(): void
    {
        $view = new ListView();
        $this->assertInstanceOf(ListView::class, $view);
    }

    public function testArrayAccess(): void
    {
        $data = [1, 2, 3];

        $view = new ListView();

        $this->assertObjectHasAttribute('vars', $view);
        $this->assertIsArray($view->vars);

        $view->vars['data'] = $data;
        $view[4] = 4;

        $this->assertTrue(isset($view[1]));
        $this->assertSame($data[2], $view[2]);
        $this->assertContains(4, $view);
        $this->assertCount(4, $view);
        unset($view[4]);
        $this->assertFalse(isset($view[4]));
    }

    public function testJsonSerialize(): void
    {
        $view = new ListView();
        $this->assertJson(json_encode($view));
    }
}
