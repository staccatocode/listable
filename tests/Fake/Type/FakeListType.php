<?php

/*
 * This file is part of staccato listable component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\Listable\Tests\Fake\Type;

use Staccato\Component\Listable\AbstractType;
use Staccato\Component\Listable\Field\TextField;
use Staccato\Component\Listable\Filter\TextFilter;
use Staccato\Component\Listable\ListBuilderInterface;

class FakeListType extends AbstractType
{
    public function buildList(ListBuilderInterface $listBuilder, array $options): void
    {
        $listBuilder->add('test_field', TextField::class);
        $listBuilder->add('test_filter', TextFilter::class);
    }
}
