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

use Staccato\Component\Listable\ListStateInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractRepository
{
    /**
     * @var array
     */
    protected $options = [];

    public function setOptions(array $options): self
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);

        return $this;
    }

    /**
     * Return result based on list state.
     *
     * @param ListStateInterface $state the list state
     */
    abstract public function getResult(ListStateInterface $state): Result;

    /**
     * Configure options for this repository using options resolver.
     */
    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
    }
}
