<?php

/*
 * This file is part of staccato list component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\ListLoader\Repository\Doctrine;

use Doctrine\ORM\EntityRepository;
use Staccato\Component\ListLoader\Repository\ListableRepository as BaseListableRepository;

class ListableRepository extends BaseListableRepository
{
    /**
     * @var EntityRepository
     */
    protected $repository;

    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {inheritdoc}.
     */
    public function find($limit = 0, $page = 0)
    {
        $limit = intval($limit);
        $limit = $limit > 0 ? $limit : 0;

        $page = intval($page);
        $page = $page > 0 ? $page : 0;

        $qb = $this->prepareQueryBuilder();

        if ($limit > 0) {
            $qb->setMaxResults($limit)
               ->setFirstResult($limit * $page);
        }

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * {inheritdoc}.
     */
    public function count()
    {
        $qb = $this->prepareQueryBuilder();
        $qb->select(sprintf('COUNT(%s)', $qb->getRootAliases()[0]));

        $count = $qb->getQuery()->getSingleScalarResult();

        return $count ? $count : 0;
    }

    /**
     * Prepare query builder.
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function prepareQueryBuilder()
    {
        $qbSetter = function ($filters, $sorter) {
            $qb = $this->createListableQueryBuilder();

            $this->setQueryBuilderFilters($qb, $filters);
            $this->setQueryBuilderSorter($qb, $sorter['name'], $sorter['type']);

            return $qb;
        };

        return $qbSetter->call($this->repository, $this->filters, $this->sorter);
    }
}
