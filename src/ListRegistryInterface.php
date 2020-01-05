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

use Staccato\Component\Listable\Exception\InvalidArgumentException;
use Staccato\Component\Listable\Field\AbstractField;
use Staccato\Component\Listable\Filter\AbstractFilter;
use Staccato\Component\Listable\Repository\AbstractRepository;

/**
 * The central registry of the Listable component.
 */
interface ListRegistryInterface
{
    /**
     * Returns instance of list type by class name or alias.
     *
     * @param string $name The class name or alias of the type
     *
     * @throws InvalidArgumentException in case of invalid type name passed
     */
    public function getListType(string $name): ListTypeInterface;

    /**
     * Returns instance of filter type class name or alias.
     *
     * @param string $name The class name or alias of the type
     *
     * @throws InvalidArgumentException in case of invalid type name passed
     */
    public function getFieldType(string $name): AbstractField;

    /**
     * Returns instance of filter type by class name or alias.
     *
     * @param string $name The class name or alias of the type
     *
     * @throws InvalidArgumentException in case of invalid type name passed
     */
    public function getFilterType(string $name): AbstractFilter;

    /**
     * Returns instance of repository by class name or alias.
     *
     * @param string $name    The class name or alias of the type
     * @param array  $options Options for repository
     *
     * @throws InvalidArgumentException in case of invalid type name passed
     */
    public function getRepository(string $name, array $options = array()): AbstractRepository;

    /**
     * Returns instance of state provider by class name or alias.
     *
     * @param string $name The class name or alias of the type
     *
     * @throws InvalidArgumentException in case of invalid type name passed
     */
    public function getStateProvider(string $name): ListStateProviderInterface;
}
