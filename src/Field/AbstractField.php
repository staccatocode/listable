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

abstract class AbstractField
{
    /**
     * @var array
     */
    private $options = array();

    /**
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $type = explode('\\', static::class);
        $resolver = new OptionsResolver();
        $resolver
            ->setDefaults(array(
                'filter' => null,
                'filter_options' => array(),
                'type' => array_pop($type),
                'visible' => true,
                'property_path' => null,
            ))
            ->setAllowedTypes('filter', array('string', 'null'))
            ->setAllowedTypes('filter_options', 'array')
            ->setAllowedTypes('visible', 'bool')
            ->setAllowedTypes('property_path', array('null', 'string'))
            ->setAllowedTypes('property_path', array('null', 'string'))
        ;

        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);

        return $this;
    }

    public function hasFilter(): bool
    {
        return (bool) $this->getFilter();
    }

    public function getFilter(): ?string
    {
        return $this->getOption('filter');
    }

    public function getFilterOptions(): array
    {
        return (array) $this->getOption('filter_options');
    }

    /**
     * @return mixed|null
     */
    public function getOption(string $name)
    {
        return $this->options[$name] ?? null;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function isVisible(): bool
    {
        return (bool) $this->getOption('visible');
    }

    public function getPropertyPath(): ?string
    {
        return $this->getOption('property_path');
    }

    public function getType(): string
    {
        return (string) $this->getOption('type');
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
    }
}
