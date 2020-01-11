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

class ArrayRepository extends AbstractRepository
{
    public function getResult(ListStateInterface $state): Result
    {
        $data = $this->options['data'];

        $data = $this->applyFilters($state, $data);
        $data = $this->applySorter($state, $data);

        $rows = 0 === $state->getLimit() ?
            $data : \array_slice($data, $state->getPage() * $state->getLimit(), $state->getLimit());

        $result = new Result();
        $result
            ->setRows($rows)
            ->setTotalCount(\count($data))
        ;

        return $result;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data' => [],
                'filter' => null,
                'sort' => null,
            ])
            ->setAllowedTypes('data', ['array'])
            ->setAllowedTypes('sort', ['closure', 'null'])
            ->setAllowedTypes('filter', ['closure', 'null'])
        ;
    }

    private function applyFilters(ListStateInterface $state, array &$data): array
    {
        if (!$state->getFilters()) {
            return $data;
        }

        if (\is_callable($this->options['filter'])) {
            return $this->options['filter']($data, $state);
        }

        $filters = $state->getFilters();
        $data = array_filter($data, static function ($row) use (&$filters) {
            foreach ($filters as $name => $filter) {
                if (!\array_key_exists($name, $row) || false !== stripos((string) $row[$name], $filter)) {
                    continue;
                }

                return false;
            }

            return true;
        });

        return $data;
    }

    private function applySorter(ListStateInterface $state, array &$data): array
    {
        if (!$state->getSorter()) {
            return $data;
        }

        if (\is_callable($this->options['sort'])) {
            return $this->options['sort']($data, $state);
        }

        $sorter = $state->getSorter();

        usort($data, static function ($a, $b) use (&$sorter) {
            $left = '';
            $right = '';
            $order = 'asc';
            foreach ($sorter as $name => $order) {
                if (\array_key_exists($name, $a)) {
                    $left .= $a[$name];
                }
                if (\array_key_exists($name, $b)) {
                    $right .= $b[$name];
                }
            }

            return 'asc' === $order ? $left <=> $right : $right <=> $left;
        });

        return $data;
    }
}
