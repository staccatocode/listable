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

use Symfony\Component\HttpFoundation\Request;

class ListRequest implements ListRequestInterface
{
    /**
     * Request.
     *
     * @var Request
     */
    protected $request;

    public function __construct(?Request $request = null)
    {
        $this->request = $request ?? Request::createFromGlobals();
    }

    /**
     * {@inheritdoc}
     */
    public function getPage(string $paramName, int $defaultValue = 0): int
    {
        $page = '' === $paramName ? $defaultValue :
            $this->request->query->getInt($paramName, $defaultValue);

        return $page > 0 ? $page : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit(string $paramName, int $defaultValue = 0): int
    {
        $limit = '' === $paramName ? $defaultValue :
            $this->request->query->getInt($paramName, $defaultValue);

        return $limit > 0 ? $limit : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(string $paramName, array $defaultValue = array()): array
    {
        $filters = $this->request->get($paramName, $defaultValue);

        return \is_array($filters) ? $this->cleanFilters($filters) : array();
    }

    /**
     * {@inheritdoc}
     */
    public function getSorter(string $paramName, array $defaultValue = array()): array
    {
        $result = array();
        $sorter = $this->request->query->get($paramName, $defaultValue);

        if (!\is_array($sorter)) {
            return $result;
        }

        foreach ($sorter as $name => $direction) {
            if (!\is_string($direction)) {
                continue;
            }
            $direction = strtolower($direction);
            if (\in_array($direction, array('asc', 'desc'))) {
                $result[$name] = $direction;
            }
        }

        return $result;
    }

    /**
     * Clean filters values.
     * Trim white chars and unset empty filters.
     *
     * @param array $filters array of filters
     */
    private function cleanFilters(array $filters): array
    {
        array_walk_recursive($filters, static function (&$item) {
            $item = trim($item);
        });

        $filters = array_filter($filters, static function (&$item) {
            if (\is_array($item)) {
                $item = array_filter($item, static function ($val) {
                    return (bool) \strlen($val);
                });

                return !empty($item);
            }

            return \strlen($item);
        });

        return $filters;
    }
}
