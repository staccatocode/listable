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

use Symfony\Component\OptionsResolver\OptionsResolver;

class ListFactory
{
    /**
     * @var ListRegistryInterface
     */
    private $registry;

    /**
     * @var int
     */
    private $listNameCounter = 0;

    /**
     * ListFactory constructor.
     */
    public function __construct(?ListRegistryInterface $registry = null)
    {
        $this->registry = $registry ?? new ListRegistry();
    }

    public function create(string $listType, array $options = array()): ListInterface
    {
        $optionsResolver = $this->getOptionsResolver();

        $type = $this->registry->getListType($listType);
        $type->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve($options);
        $builder = $this->createBuilderFromOptions($options);
        $builder->setType($type);

        $type->buildList($builder, $options);

        return $builder->getList();
    }

    private function createBuilderFromOptions(array $options): ListBuilderInterface
    {
        $builder = new ListBuilder($this->registry);
        $builder
            ->setOptions($options)
            ->setName($options['name'])
            ->setLimit($options['limit'])
            ->setLimitParam($options['limit_param'], $options['limit_param_options'] ?? array())
            ->setPageParam($options['page_param'])
            ->setSorterParam($options['sorter_param'])
            ->setFiltersParam($options['filters_param'])
            ->setPersistState($options['state_persist'])
            ->setStateProvider($options['state_provider'])
        ;

        return $builder;
    }

    private function getOptionsResolver(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setDefaults(array(
                'name' => $this->getDefaultListName(),
                'limit' => 20,
                'limit_min' => 0,
                'limit_max' => 0,
                'limit_param' => 'limit',
                'page_param' => 'page',
                'sorter_param' => 'order',
                'filters_param' => 'search',
                'state_provider' => ListStateProvider::class,
                'state_persist' => false,
            ))
            ->setAllowedTypes('limit', array('int'))
            ->setAllowedTypes('limit_min', array('int'))
            ->setAllowedTypes('limit_max', array('int'))
            ->setAllowedTypes('limit_param', array('string', 'null'))
            ->setAllowedTypes('page_param', array('string', 'null'))
            ->setAllowedTypes('filters_param', array('string', 'null'))
            ->setAllowedTypes('sorter_param', array('string', 'null'))
            ->setAllowedValues('limit', static function ($value) {
                return $value >= 0;
            })
            ->setAllowedValues('limit_min', static function ($value) {
                return $value >= 0;
            })
            ->setAllowedValues('limit_max', static function ($value) {
                return $value >= 0;
            })
        ;

        return $resolver;
    }

    private function getDefaultListName(): string
    {
        return 'list-' . substr(md5(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) . $this->listNameCounter++), 0, 6);
    }
}
