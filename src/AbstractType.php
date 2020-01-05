<?php

/*
 * This file is part of staccato listable component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\Listable;

use Symfony\Component\OptionsResolver\OptionsResolver;

class AbstractType implements ListTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildList(ListBuilderInterface $builder, array $options): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(ListView $view, ListInterface $list, array $options): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
