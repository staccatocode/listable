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
     * {@inheritdoc}
     */
    public function find($limit = 0, $page = 0)
    {
        $limit = intval($limit);
        $limit = $limit > 0 ? $limit : 0;

        $page = intval($page);
        $page = $page > 0 ? $page : 0;

        $qb = $this->prepareQueryBuilder(true, true);

        if ($limit > 0) {
            $qb->setMaxResults($limit)
               ->setFirstResult($limit * $page);
        }

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $qb = $this->prepareQueryBuilder(true, false);
        $qb->select(sprintf('COUNT(%s)', $qb->getRootAliases()[0]));

        $count = $qb->getQuery()->getSingleScalarResult();

        return $count ? (int) $count : 0;
    }

    /**
     * Prepare query builder.
     *
     * @param bool $includeFilters
     * @param bool $includeSorter
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function prepareQueryBuilder($includeFilters = true, $includeSorter = true)
    {
        $qbSetter = function ($filters, $sorter, $includeFilters, $includeSorter) {
            $qb = $this->createListableQueryBuilder();

            if ($includeFilters) {
                $this->setQueryBuilderFilters($qb, $filters);
            }
            if ($includeSorter) {
                $this->setQueryBuilderSorter($qb, $sorter['name'], $sorter['type']);
            }

            return $qb;
        };

        return $qbSetter->call($this->repository, $this->filters, $this->sorter, $includeFilters, $includeSorter);
    }
}
