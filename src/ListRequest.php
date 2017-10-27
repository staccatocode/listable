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
use Symfony\Component\HttpFoundation\Session\Session;

class ListRequest implements ListRequestInterface
{
    /**
     * Session storage.
     *
     * @var Session
     */
    public $session;

    /**
     * Request.
     *
     * @var Request
     */
    public $request;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        $this->session = new Session();
    }

    /**
     * {@inheritdoc}
     */
    public function getPage(string $paramName): int
    {
        if ('' !== $paramName) {
            $page = $this->request->query->getInt($paramName, 0);
        }

        return $page > 0 ? $page : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(string $paramName, string $filterSource): array
    {
        $filters = array();
        $filterSource = strtolower($filterSource);

        if ('session' === $filterSource) {
            $filters = $this->session->get('st.list.'.$paramName, array());
        } elseif ('get' === $filterSource) {
            $filters = $this->request->query->get($paramName, array());
        }

        return $this->cleanFilters($filters);
    }

    /**
     * {@inheritdoc}
     */
    public function getSorter(string $paramAsc, string $paramDesc): array
    {
        $result = array(
            'name' => null,
            'type' => null,
        );

        if ($this->request->query->has($paramAsc)) {
            $result['name'] = $this->request->query->get($paramAsc);
            $result['type'] = 'asc';
        } elseif ($this->request->query->has($paramDesc)) {
            $result['name'] = $this->request->query->get($paramDesc);
            $result['type'] = 'desc';
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function storeFilters(string $paramName, array $filters): ListRequestInterface
    {
        $this->session->set('st.list.'.$paramName, $filters);

        return $this;
    }

    /**
     * Clean filters values.
     * Trim white chars and unset empty filters.
     *
     * @param array $filters array of filters
     *
     * @return array
     */
    protected function cleanFilters($filters): array
    {
        if (!is_array($filters)) {
            return array();
        }

        array_walk_recursive($filters, function (&$item, $key) {
            $item = trim($item);
        });

        $filters = array_filter($filters, function (&$item) {
            if (is_array($item)) {
                $item = array_filter($item, 'strlen');

                return !empty($item);
            }

            return strlen($item);
        });

        return $filters;
    }
}
