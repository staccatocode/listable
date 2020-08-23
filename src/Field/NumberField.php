<?php

/*
 * This file is part of staccato listable component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\Listable\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;

class NumberField extends AbstractField
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'precision' => 2,
                'decimal_point' => '.',
                'thousands_separator' => '',
                'render' => function ($value) {
                    return number_format(
                        $value,
                        $this->getOption('precision'),
                        $this->getOption('decimal_point'),
                        $this->getOption('thousands_separator')
                    );
                },
            ])
            ->setAllowedTypes('precision', 'int')
            ->remove('normalize')
        ;
    }

    public function normalize($value, $context): float
    {
        return (float) $value;
    }
}
