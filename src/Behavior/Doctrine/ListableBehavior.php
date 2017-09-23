<?php

/*
 * This file is part of staccato list component
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Component\Listable\Behavior\Doctrine;

use Doctrine\ORM\QueryBuilder;
use Staccato\Component\Listable\Repository\Doctrine\ListableRepository;

trait ListableBehavior
{
    /**
     * Create ListableRepository based on this repository.
     *
     * @return ListableRepository
     */
    public function createList(): ListableRepository
    {
        return new ListableRepository($this);
    }

    /**
     * Create new instance of QueryBuilder.
     *
     * @return QueryBuilder
     */
    protected function createListableQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('e');
    }

    /**
     * Set filters on QueryBuilder.
     *
     * @param QueryBuilder $qb
     * @param array        $filters
     */
    protected function setQueryBuilderFilters(QueryBuilder $qb, array $filters): void
    {
        // Should be overriden in repository
    }

    /**
     * Set sorting on QueryBilder.
     *
     * @param QueryBuilder $qb
     * @param string|null  $name sorter name
     * @param string       $type sorter type `asc` ir `desc`
     */
    protected function setQueryBuilderSorter(QueryBuilder $qb, ?string $name, string $type): void
    {
        // Should be overriden in repository
    }

    /**
     * Ensure column prefix exists and append if not.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     *
     * @return string column name with prefix
     */
    protected function columnPrefix(QueryBuilder $qb, string $columnName): string
    {
        if (false === strpos($columnName, '.')) {
            $columnName = $qb->getRootAliases()[0].'.'.$columnName;
        }

        return (string) $columnName;
    }

    /**
     * Apply ordering on QueryBuilder.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName column name
     * @param string       $type       type of sort (asc or desc)
     * @param bool         $add        add order
     *
     * @return self
     */
    protected function orderBy(QueryBuilder $qb, string $columnName, string $type, bool $add = false)
    {
        $type = strtoupper($type);

        if ('ASC' !== $type && 'DESC' !== $type) {
            $type = 'ASC';
        }

        $orderBy = $add ? 'addOrderBy' : 'orderBy';

        $qb->$orderBy($this->columnPrefix($qb, $columnName), $type);

        return $this;
    }

    /**
     * Apply like filter on QueryBuilder.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     * @param bool         $or         append as or/and
     * @param bool         $not        append as not like
     *
     * @return self
     */
    protected function like(QueryBuilder $qb, string $columnName, $value, bool $or = false, bool $not = false)
    {
        $where = $or ? 'orWhere' : 'andWhere';
        $like = $not ? 'notLike' : 'like';

        $qb->$where(
            $qb->expr()->$like(
                $this->columnPrefix($qb, $columnName),
                $qb->expr()->literal($value)
            )
        );

        return $this;
    }

    /**
     * Apply equal filter on QueryBuilder.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     * @param bool         $or         append as or/and
     * @param bool         $not        append as not like
     *
     * @return self
     */
    protected function equal(QueryBuilder $qb, string $columnName, $value, bool $or = false, bool $not = false)
    {
        $where = $or ? 'orWhere' : 'andWhere';

        if (is_array($value)) {
            $eq = $not ? 'notIn' : 'in';
        } else {
            $eq = $not ? 'neq' : 'eq';
            $value = $qb->expr()->literal($value);
        }

        $qb->$where(
             $qb->expr()->$eq(
                 $this->columnPrefix($qb, $columnName),
                 $value
             )
         );

        return $this;
    }

    /**
     * Apply comparission filter on QueryBuilder.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     * @param bool         $or         append as or/and
     * @param bool         $equal      qreater-equal or greater
     *
     * @return self
     */
    protected function greaterThan(QueryBuilder $qb, $columnName, $value, bool $or = false, bool $equal = false)
    {
        $where = $or ? 'orWhere' : 'andWhere';
        $cmp = $equal ? 'gte' : 'gt';

        $qb->$where(
            $qb->expr()->$cmp(
                $this->columnPrefix($qb, $columnName),
                $qb->expr()->literal($value)
            )
        );

        return $this;
    }

    /**
     * Apply comparission filter on QueryBuilder.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     * @param bool         $or         append as or/and
     * @param bool         $equal      less-equal or less
     *
     * @return self
     */
    protected function lessThan(QueryBuilder $qb, $columnName, $value, $or = false, $equal = false)
    {
        $where = $or ? 'orWhere' : 'andWhere';
        $cmp = $equal ? 'lte' : 'lt';

        $qb->$where(
            $qb->expr()->$cmp(
                $this->columnPrefix($qb, $columnName),
                $qb->expr()->literal($value)
            )
        );

        return $this;
    }

    /**
     * Apply date filter on QueryBuilder.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $dateOrRange optionally array with from/to keys
     *                                  and string or \DateTime as value
     *
     * @return self
     */
    protected function dateRange($qb, $columnName, $dateOrRange)
    {
        if (is_array($dateOrRange)) {
            if (isset($dateOrRange['from']) && is_string($dateOrRange['from'])) {
                try {
                    $dateOrRange['from'] = new \DateTime($dateOrRange['from']);
                } catch (\Exception $e) {
                    unset($dateOrRange['from']);
                }
            }

            if (isset($dateOrRange['to']) && is_string($dateOrRange['to'])) {
                try {
                    $dateOrRange['to'] = new \DateTime($dateOrRange['to']);
                } catch (\Exception $e) {
                    unset($dateOrRange['to']);
                }
            }
        } elseif (is_string($dateOrRange)) {
            try {
                $dateOrRange = new \DateTime($dateOrRange);
            } catch (\Exception $e) {
                $dateOrRange = null;
            }
        } else {
            $dateOrRange = null;
        }

        if ($dateOrRange instanceof \DateTime) {
            $dateOrRange = array(
                'from' => $dateOrRange,
                'to' => $dateOrRange,
            );
        }

        if (is_array($dateOrRange)) {
            if (isset($dateOrRange['from']) && $dateOrRange['from'] instanceof \DateTime) {
                $this->andGreaterThanEqual($qb, $columnName, $dateOrRange['from']->format('Y-m-d').' 00:00:00');
            }

            if (isset($dateOrRange['to']) && $dateOrRange['to'] instanceof \DateTime) {
                $this->andLessThanEqual($qb, $columnName, $dateOrRange['to']->format('Y-m-d').' 23:59:59');
            }
        }

        return $this;
    }

    /**
     * Alias for greaterThan.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     *
     * @return self
     */
    protected function andGreaterThan(QueryBuilder $qb, string $columnName, $value)
    {
        return $this->greaterThan($qb, $columnName, $value, false, false);
    }

    /**
     * Alias for greaterThan.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     *
     * @return self
     */
    protected function orGreaterThan(QueryBuilder $qb, string $columnName, $value)
    {
        return $this->greaterThan($qb, $columnName, $value, true, false);
    }

    /**
     * Alias for greaterThanEqual.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     *
     * @return self
     */
    protected function andGreaterThanEqual(QueryBuilder $qb, string $columnName, $value)
    {
        return $this->greaterThan($qb, $columnName, $value, false, true);
    }

    /**
     * Alias for greaterThanEqual.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     *
     * @return self
     */
    protected function orGreaterThanEqual(QueryBuilder $qb, string $columnName, $value)
    {
        return $this->greaterThan($qb, $columnName, $value, true, true);
    }

    /**
     * Alias for lessThan.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     *
     * @return self
     */
    protected function andLessThan(QueryBuilder $qb, string $columnName, $value)
    {
        return $this->lessThan($qb, $columnName, $value, false, false);
    }

    /**
     * Alias for lessThan.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     *
     * @return self
     */
    protected function orLessThan(QueryBuilder $qb, string $columnName, $value)
    {
        return $this->lessThan($qb, $columnName, $value, true, false);
    }

    /**
     * Alias for lessThanEqual.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     *
     * @return self
     */
    protected function andLessThanEqual(QueryBuilder $qb, string $columnName, $value)
    {
        return $this->lessThan($qb, $columnName, $value, false, true);
    }

    /**
     * Alias for lessThanEqual.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     *
     * @return self
     */
    protected function orLessThanEqual(QueryBuilder $qb, string $columnName, $value)
    {
        return $this->lessThan($qb, $columnName, $value, true, true);
    }

    /**
     * Alias for like.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     *
     * @return self
     */
    protected function andLike(QueryBuilder $qb, string $columnName, $value)
    {
        return $this->like($qb, $columnName, $value, false, false);
    }

    /**
     * Alias for like.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     *
     * @return self
     */
    protected function orLike(QueryBuilder $qb, string $columnName, $value)
    {
        return $this->like($qb, $columnName, $value, true, false);
    }

    /**
     * Alias for like.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     *
     * @return self
     */
    protected function andNotLike(QueryBuilder $qb, string $columnName, $value)
    {
        return $this->like($qb, $columnName, $value, false, true);
    }

    /**
     * Alias for like.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     *
     * @return self
     */
    protected function orNotLike(QueryBuilder $qb, string $columnName, $value)
    {
        return $this->like($qb, $columnName, $value, true, true);
    }

    /**
     * Alias for equal.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     *
     * @return self
     */
    protected function andEqual(QueryBuilder $qb, string $columnName, $value)
    {
        return $this->equal($qb, $columnName, $value, false, false);
    }

    /**
     * Alias for equal.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     *
     * @return self
     */
    protected function orEqual(QueryBuilder $qb, string $columnName, $value)
    {
        return $this->equal($qb, $columnName, $value, true, false);
    }

    /**
     * Alias for equal.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     *
     * @return self
     */
    protected function andNotEqual(QueryBuilder $qb, string $columnName, $value)
    {
        return $this->equal($qb, $columnName, $value, false, true);
    }

    /**
     * Alias for equal.
     *
     * @param QueryBuilder $qb
     * @param string       $columnName
     * @param mixed        $value
     *
     * @return self
     */
    protected function orNotEqual(QueryBuilder $qb, string $columnName, $value)
    {
        return $this->equal($qb, $columnName, $value, true, true);
    }
}
