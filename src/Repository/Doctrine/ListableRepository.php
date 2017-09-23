<?php

/*
 * This file is part of staccato list component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\Listable\Repository\Doctrine;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Staccato\Component\Listable\Repository\ListableRepository as BaseListableRepository;

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
    public function find(int $limit = 0, int $page = 0)
    {
        $limit = (int) $limit;
        $limit = $limit > 0 ? $limit : 0;

        $page = (int) $page;
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
    public function count(): int
    {
        $qb = $this->prepareQueryBuilder(true, false);

        $counter = function ($qb) {
            $resultSetMapping = new ResultSetMapping();
            $resultSetMapping->addScalarResult('COUNT(*)', 'count');

            $query = $this->getEntityManager()->createNativeQuery(
                // TODO: subquery can be unoptimised for simpler queries,
                // though it's probably the only option for those complex.
                sprintf('SELECT COUNT(*) FROM (%s) counter', $qb->getQuery()->getSql()),
                $resultSetMapping
            );

            $count = $query->getSingleScalarResult();
            $count = $count ? $count : 0;

            return (int) $count;
        };

        return $counter->call($this->repository, $qb);
    }

    /**
     * Create QueryBuilder and applay filters and/or sorter.
     *
     * @param bool $includeFilters apply filters if true
     * @param bool $includeSorter  apply sorter if true
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
