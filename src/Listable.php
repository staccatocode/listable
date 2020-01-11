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

class Listable implements ListInterface
{
    /**
     * @var ListConfigInterface
     */
    private $config;

    /**
     * @var ListStateInterface
     */
    private $state;

    /**
     * @var iterable
     */
    private $data = [];

    /**
     * @var int
     */
    private $page = 0;

    /**
     * @var int
     */
    private $totalPages = 0;

    /**
     * @var int
     */
    private $totalCount = 0;

    public function __construct(ListConfigInterface $config, ListStateInterface $state)
    {
        $this->config = $config;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(): ListConfigInterface
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function getState(): ListStateInterface
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function createView(): ListView
    {
        $view = new ListView();
        $view->vars['config'] = $this->getConfig();
        $view->vars['state'] = $this->getState();
        $view->vars['data'] = $this->getData();
        $view->vars['pagination']['count'] = \count($view->vars['data']);
        $view->vars['pagination']['total'] = $this->getTotalCount();
        $view->vars['pagination']['pages'] = $this->getTotalPages();
        $view->vars['pagination']['page'] = $this->getPage();
        $view->vars['pagination']['limit'] = $this->getState()->getLimit();

        $type = $this->getConfig()->getType();
        if ($type) {
            $type->buildView($view, $this, $this->getConfig()->getOptions());
        }

        return $view;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): iterable
    {
        return $this->data;
    }

    public function setData(iterable $data): ListInterface
    {
        $this->data = $data;

        return $this;
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
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function setTotalPages(int $totalPages): self
    {
        $this->totalPages = $totalPages;

        return $this;
    }

    public function setTotalCount(int $totalCount): self
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    public function checkPageOverflow(): bool
    {
        return $this->getState()->getPage() > 0 && $this->getState()->getPage() > $this->getPage();
    }
}
