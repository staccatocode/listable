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

use Staccato\Component\Listable\Repository\Exception\InvalidRepositoryFactoryException;

class RepositoryFactory
{
    /**
     * @var array
     */
    private $factories = array();

    /**
     * Create repository.
     *
     * @param string $name factory name
     * @param mixed  $data factory data
     *
     * @return AbstractRepository
     */
    public function create(string $name, $data = null): AbstractRepository
    {
        if (!$this->has($name)) {
            throw new InvalidRepositoryFactoryException(sprintf(
                'Factory `%s` does not exists.', $name
            ));
        }

        return $this->factories[$name]->create($data);
    }

    /**
     * Add repository factory.
     *
     * @param string                     $name    factory name
     * @param RepositoryFactoryInterface $factory
     */
    public function add(string $name, RepositoryFactoryInterface $factory): void
    {
        $this->factories[$name] = $factory;
    }

    /**
     * Check if factory exists.
     *
     * @param string $name factory name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->factories[$name]);
    }

    /**
     * Remove repository factory.
     *
     * @param string $name factory name
     */
    public function remove(string $name): void
    {
        if ($this->has($name)) {
            unset($this->factories[$name]);
        }
    }
}
