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

interface ListTypeInterface
{
    /**
     * Build list using builder and options.
     */
    public function buildList(ListBuilderInterface $builder, array $options): void;

    /**
     * Build list view.
     */
    public function buildView(ListView $view, ListInterface $list, array $options): void;

    /**
     * Configure default or required options needed to build this list.
     */
    public function configureOptions(OptionsResolver $resolver): void;
}
