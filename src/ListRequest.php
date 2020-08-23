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

use Staccato\Component\Listable\Helper\ArrayCleaner;
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
    public function getFilters(string $paramName, array $defaultValue = []): array
    {
        $filters = $this->request->get($paramName, $defaultValue);

        return \is_array($filters) ? ArrayCleaner::clean($filters) : [];
    }

    /**
     * {@inheritdoc}
     */
    public function getSorter(string $paramName, array $defaultValue = []): array
    {
        $result = [];
        $sorter = $this->request->query->get($paramName, $defaultValue);

        if (!\is_array($sorter)) {
            return $result;
        }

        foreach ($sorter as $name => $direction) {
            if (!\is_string($direction)) {
                continue;
            }
            $direction = strtolower($direction);
            if (\in_array($direction, ['asc', 'desc'])) {
                $result[$name] = $direction;
            }
        }

        return $result;
    }
}
