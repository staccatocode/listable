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

use Staccato\Component\Listable\Repository\AbstractRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListObject implements ListInterface
{
    /**
     * The list's configuration.
     *
     * @var ListConfigInterface
     */
    private $config;

    /**
     * Request object for handling List actions.
     *
     * @var Request
     */
    private $request;

    /**
     * Total number of rows on last load.
     *
     * @var int
     */
    private $count = 0;

    /**
     * Currently loaded page number.
     *
     * @var int
     */
    private $page = 0;

    /**
     * Loaded data.
     *
     * @var array
     */
    private $data = array();

    public function __construct(ListConfigInterface $config)
    {
        $this->config = $config;
        $this->request = Request::createFromGlobals();
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * {@inheritdoc}
     */
    public function countPages(): int
    {
        return (int) (ceil($this->getLimit() > 0 ? ($this->count() / $this->getLimit()) : 0));
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->config->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageParam(): string
    {
        return (string) $this->config->getPageParam();
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterSource(): string
    {
        return $this->config->getFilterSource();
    }

    /**
     * {@inheritdoc}
     */
    public function getSorterParams(): array
    {
        return $this->config->getSorterParams();
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit(): int
    {
        return (int) $this->config->getLimit();
    }

    /**
     * {@inheritdoc}
     */
    public function getLimitParam(): string
    {
        return (string) $this->config->getLimitParam();
    }

    /**
     * {@inheritdoc}
     */
    public function getActionParam(): string
    {
        return (string) $this->config->getActionParam();
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(): AbstractRepository
    {
        return $this->config->getRepository();
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        return $this->config->getOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function on(string $action, callable $handler): ListInterface
    {
        if ($this->request instanceof Request) {
            if ($this->request->isMethod('post') && $this->request->request->has($this->getActionParam())) {
                $params = $this->request->request->get($this->getActionParam(), array());
                $params = is_array($params) ? $params : array();

                $listAction = isset($params['action']) ? $params['action'] : null;
                $listName = isset($params['name']) ? $params['name'] : null;

                if ($action === $listAction && $this->getName() === $listName) {
                    $result = $handler($this, $params, $this->request);

                    if ($result instanceof Response) {
                        $result->send();
                    }
                }
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function load(): ListInterface
    {
        $repository = $this->getRepository();

        if ($repository instanceof AbstractRepository) {
            $options = $this->getOptions();

            $repository->setFilters($options['filters']);
            $repository->orderBy($options['sorter']['name'], $options['sorter']['type']);

            $this->count = $repository->count();

            $countPages = $this->countPages();
            $requestPage = (int) $this->config->getPage();

            if ($countPages > 0 && $requestPage + 1 > $countPages) {
                // If we load non-existent page go back to last page
                $this->page = $countPages - 1;
            } else {
                $this->page = 0 === $countPages ? 0 : $requestPage;
            }

            $this->data = $repository->find($this->getLimit(), $this->getPage());
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function createView(): ListView
    {
        $view = new ListView();

        $view->vars['name'] = $this->getName();
        $view->vars['data'] = $this->getData();
        $view->vars['options'] = $this->getOptions();
        $view->vars['params']['action'] = $this->getActionParam();
        $view->vars['params']['limit'] = $this->getLimitParam();
        $view->vars['params']['page'] = $this->getPageParam();
        $view->vars['params']['sorter'] = $this->getSorterParams();
        $view->vars['pagination']['count'] = count($view->vars['data']);
        $view->vars['pagination']['total'] = $this->count();
        $view->vars['pagination']['pages'] = $this->countPages();
        $view->vars['pagination']['page'] = $this->getPage();
        $view->vars['pagination']['limit'] = $this->getLimit();

        return $view;
    }
}
