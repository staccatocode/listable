<?php

/*
 * This file is part of staccato listable component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\Listable\Repository;

use Staccato\Component\Listable\Exception\ClassNotFoundException;
use Staccato\Component\Listable\Repository;

class ClassRepositoryFactory implements RepositoryFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create($data = array()): AbstractRepository
    {
        $class = null;
        $arguments = array();

        if (is_array($data)) {
            $class = $data[0];
            $arguments = isset($data[1]) ? $data[1] : array();
            $arguments = is_array($arguments) ? $arguments : array($arguments);
        } elseif (is_string($data)) {
            $class = $data;
        }

        if (!class_exists($class)) {
            throw new ClassNotFoundException(sprintf(
                'Repository class `%s` does not exists.', $class
            ));
        }

        return new $class(...$arguments);
    }
}
