<?php

/*
 * This file is part of staccato listable component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\Listable\Helper;

class ArrayCleaner
{
    public static function clean(array $data): array
    {
        foreach ($data as $k => $d) {
            if (\is_array($d)) {
                $data[$k] = self::clean($d);

                if (0 === \count($data[$k])) {
                    unset($data[$k]);
                }
            } else {
                if (\is_bool($d)) {
                    $d = (string) (int) $d;
                }

                $data[$k] = trim($d);

                if (0 === \strlen($data[$k])) {
                    unset($data[$k]);
                }
            }
        }

        return $data;
    }
}
