<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Pagination;

use Illuminate\Pagination\LengthAwarePaginator;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Aggregation\Builder as AggregationBuilder;
use Epsoftware\Laravel\Doctrine\Mongo\Pagination\PaginatorAdapter;

trait PaginatesFromRequest
{
    /**
     * @param int    $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
    */
    public function paginateAll($perPage = 15, $pageName = 'page'): LengthAwarePaginator
    {
        $builder = $this->createQueryBuilder();
        $aggregation = $this->createAggregationBuilder();

        return $this->paginate($builder, $perPage, $pageName, $aggregation);
    }

    /**
     * @param Builder            $builder
     * @param int                $perPage
     * @param string             $pageName
     * @param AggregationBuilder $aggregation
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
    */
    public function paginate(Builder $builder, $perPage, $pageName = 'page', AggregationBuilder $aggregation): LengthAwarePaginator
    {
        return PaginatorAdapter::fromRequest(
            $builder,
            $perPage,
            $pageName,
            $aggregation
        )->make();
    }

    /**
     * Creates a new QueryBuilder instance that is prepopulated for this entity name.
     *
     * @return \Doctrine\ODM\MongoDB\Query\Builder
    */
    abstract public function createQueryBuilder();

    /**
     * Creates a new AggregationBuilder instance that is prepopulated for this entity name.
     *
     * @return \Doctrine\ODM\MongoDB\Aggregation\Builder
    */
    abstract public function createAggregationBuilder();
}