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

interface RepositoryFactoryInterface
{
    /**
     * Create repository.
     *
     * @param mixed $data any kind of data needed to create repository
     *
     * @return AbstractRepository
     */
    public function create($data): AbstractRepository;
}
