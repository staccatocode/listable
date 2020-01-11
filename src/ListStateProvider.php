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

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ListStateProvider implements ListStateProviderInterface
{
    /**
     * @var ListRequestInterface
     */
    private $request;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * ListStateProvider constructor.
     */
    public function __construct(?ListRequestInterface $listRequest = null, ?SessionInterface $session = null)
    {
        $this->session = $session ?? new Session();
        $this->request = $listRequest ?? new ListRequest();
    }

    public function getState(ListConfigInterface $config): ListState
    {
        if ($config->getPersistState()) {
            $state = $this->getSessionState($config);
        } else {
            $page = $config->getPageParam() ? $this->request->getPage($config->getPageParam(), $config->getPage()) : $config->getPage();
            $limit = $config->getLimitParam() ? $this->request->getLimit($config->getLimitParam(), $config->getLimit()) : $config->getLimit();

            $state = new ListState($page, $limit);
        }

        $requestFilters = $this->request->getFilters($config->getFiltersParam(), $state->getFilters());
        $filters = [];

        foreach ($config->getFilters() as $name => $filter) {
            $value = $filter->isLocked() ? $filter->getData() : ($requestFilters[$name] ?? $filter->getData());
            if ($value && $filter->isValid($value)) {
                $filters[$name] = $requestFilters[$name];
            }
        }

        $state->setFilters($filters);
        $state->setSorter($this->request->getSorter($config->getSorterParam(), $state->getSorter()));

        if ($config->getPersistState()) {
            $this->setSessionState($config, $state);
        }

        return $state;
    }

    private function setSessionState(ListConfigInterface $config, ListState $state): void
    {
        $this->session->set($this->getSessionStateKey($config), $state->toArray());
    }

    private function getSessionState(ListConfigInterface $config): ListState
    {
        $data = $this->session->get($this->getSessionStateKey($config), []);

        $page = $config->getPageParam() ? $this->request->getPage($config->getPageParam(), $data['page'] ?? $config->getPage()) : $config->getPage();
        $limit = $config->getLimitParam() ? $this->request->getLimit($config->getLimitParam(), $data['limit'] ?? $config->getLimit()) : $config->getLimit();

        $state = new ListState(0, 0);
        $state->fromArray($data);
        $state->setPage($page);
        $state->setLimit($limit);

        return $state;
    }

    private function getSessionStateKey(ListConfigInterface $config): string
    {
        return 'staccato.listable.' . $config->getName() . '.state';
    }
}
