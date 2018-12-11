<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Pagination;

use Illuminate\Pagination\LengthAwarePaginator;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Aggregation\Builder as AggregationBuilder;
use Epsoftware\Laravel\Doctrine\Mongo\Pagination\PaginatorAdapter;

trait PaginatesFromParams
{
    /**
     * @param int $perPage
     * @param int $page
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
    */
    public function paginateAll($perPage = 15, $page = 1): LengthAwarePaginator
    {
        $builder = $this->createQueryBuilder();
        $aggregation = $this->createAggregationBuilder();

        return $this->paginate($builder, $perPage, $page, $aggregation);
    }

    /**
     * @param Builder            $builder
     * @param int                $perPage
     * @param int                $page
     * @param AggregationBuilder $aggregation
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
    */
    public function paginate(Builder $builder, $perPage, $page = 1, $aggregation): LengthAwarePaginator
    {
        return PaginatorAdapter::fromParams(
            $builder,
            $perPage,
            $page,
            $aggregation
        )->make();
    }

    /**
     * Creates a new QueryBuilder instance that is prepopulated for this entity name.
     *
     * @return \Doctrine\ODM\MongoDB\Query\Builder
    */
    abstract public function createQueryBuilder(): Builder;

    /**
     * Creates a new AggregationBuilder instance that is prepopulated for this entity name.
     *
     * @return \Doctrine\ODM\MongoDB\Aggregation\Builder
    */
    abstract public function createAggregationBuilder(): AggregationBuilder;
}