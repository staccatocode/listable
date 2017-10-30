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
use Staccato\Component\Listable\Repository\Exception\InvalidRepositoryException;

class ListBuilder extends ListConfigBuilder implements ListBuilderInterface
{
    /**
     * The list's request.
     *
     * @var ListRequestInterface
     */
    protected $listRequest;

    /**
     * @param ListRequestInterface $listRequest The list request object
     */
    public function __construct(ListRequestInterface $listRequest)
    {
        $this->listRequest = $listRequest;
        $this->setSorter(null, null);
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidRepositoryException
     */
    public function getList(): ListInterface
    {
        $repository = $this->getRepository();

        if (!$repository instanceof AbstractRepository) {
            throw new InvalidRepositoryException(sprintf(
                'Repository must be instance of `%s` instance of `%s` given.',
                AbstractRepository::class, null === $repository ? 'NULL' : get_class($repository)
            ));
        }

        if ('' !== $this->getPageParam()) {
            $this->setPage($this->listRequest->getPage($this->getPageParam(), $this->getPage()));
        }

        if ('' !== $this->getLimitParam()) {
            $this->setLimit($this->listRequest->getLimit($this->getLimitParam(), $this->getLimit()));
        }

        $this->mergeOptions();

        $list = $this->createList();
        $list->load();

        return $list;
    }

    /**
     * Create new instance of list.
     *
     * @return ListObject
     */
    protected function createList()
    {
        return new ListObject($this->getListConfig());
    }

    /**
     * Merge filters and sorter options from
     * builder and list request.
     *
     * @return self
     */
    protected function mergeOptions()
    {
        $options = $this->getOptions();
        $sorter = $options['sorter'];
        $sorterNames = $this->getSorterParams();

        $this->setFilters($this->listRequest->getFilters(
            $this->getName(),
            $this->getFilterSource()
        ));

        $requestSorter = $this->listRequest->getSorter(
            isset($sorterNames['asc']) ? $sorterNames['asc'] : 'asc',
            isset($sorterNames['desc']) ? $sorterNames['desc'] : 'desc'
        );

        if (isset($requestSorter['name'])) {
            $sorter['name'] = $requestSorter['name'];
        }

        if (isset($requestSorter['type'])) {
            $sorter['type'] = $requestSorter['type'];
        }

        $this->setSorter($sorter['name'], $sorter['type']);

        return $this;
    }
}
