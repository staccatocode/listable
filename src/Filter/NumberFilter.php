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

class NumberFilter extends AbstractFilter
{
    public function isValid($value): bool
    {
        if (\is_array($value)) {
            $hasFrom = \array_key_exists('from', $value);
            $hasTo = \array_key_exists('to', $value);

            if ($hasFrom && !is_numeric($value['from'])) {
                return false;
            }

            if ($hasTo && !is_numeric($value['to'])) {
                return false;
            }

            return $hasFrom || $hasTo;
        }

        return is_numeric($value);
    }
}
