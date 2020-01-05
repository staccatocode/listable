<?php

/*
 * This file is part of staccato listable component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\Listable\Filter;

class TextFilter extends AbstractFilter
{
    public function isValid($value): bool
    {
        return \is_string($value);
    }
}
