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

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractFilter
{
    /**
     * @var array
     */
    private $options = [];

    /**
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $type = explode('\\', static::class);

        $resolver = new OptionsResolver();
        $resolver
            ->setDefaults([
                'data' => null,
                'field' => null,
                'label' => '',
                'visible' => true,
                'locked' => false,
                'type' => array_pop($type),
            ])
            ->setAllowedTypes('field', ['string', 'null'])
            ->setAllowedTypes('label', 'string')
            ->setAllowedTypes('visible', 'bool')
            ->setAllowedTypes('locked', 'bool')
        ;

        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);

        return $this;
    }

    public function hasField(): bool
    {
        return (bool) $this->getField();
    }

    public function getField(): ?string
    {
        return $this->getOption('field');
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

    public function isLocked(): bool
    {
        return (bool) $this->getOption('locked');
    }

    public function getLabel(): string
    {
        return (string) $this->getOption('label');
    }

    public function getType(): string
    {
        return (string) $this->getOption('type');
    }

    /**
     * @return mixed|null
     */
    public function getData()
    {
        return $this->getOption('data');
    }

    /**
     * @param mixed $value
     */
    abstract public function isValid($value): bool;

    protected function configureOptions(OptionsResolver $resolver): void
    {
    }
}
