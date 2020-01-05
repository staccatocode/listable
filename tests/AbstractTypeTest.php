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
use Staccato\Component\Listable\AbstractType;
use Staccato\Component\Listable\ListBuilderInterface;
use Staccato\Component\Listable\ListTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @covers \Staccato\Component\Listable\AbstractType
 */
class AbstractTypeTest extends TestCase
{
    public function testCreate(): void
    {
        $listBuilder = $this->getMockBuilder(ListBuilderInterface::class)->getMock();
        $optionsResolver = $this->getMockBuilder(OptionsResolver::class)->getMock();
        $mockList = $listBuilder->getList();

        $type = new class() extends AbstractType {
        };

        $type->configureOptions($optionsResolver);
        $type->buildList($listBuilder, array());
        $type->buildView($mockList->createView(), $mockList, array());

        $this->assertInstanceOf(AbstractType::class, $type);
        $this->assertInstanceOf(ListTypeInterface::class, $type);
    }
}
