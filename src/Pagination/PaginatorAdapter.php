<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Pagination;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Doctrine\ODM\MongoDB\Query\Query;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Aggregation\Builder as AggregationBuilder;

class PaginatorAdapter
{
    /**
     * @var Builder
    */
    protected $builder;

    /**
     * @var AggregationBuilder
    */
    protected $aggregation;

    /**
     * @var int
    */
    private $perPage;

    /**
     * @var callable
    */
    private $pageResolver;

    /**
     * @param Builder            $builder
     * @param int                $perPage
     * @param callable           $pageResolver
     * @param AggregationBuilder $aggregation
    */
    private function __construct(Builder $builder, $perPage, $pageResolver, AggregationBuilder $aggregation)
    {
        $this->builder      = $builder;
        $this->perPage      = $perPage;
        $this->pageResolver = $pageResolver;
        $this->aggregation  = $aggregation;
    }

    /**
     * @param Builder            $builder
     * @param int                $perPage
     * @param string             $pageName
     * @param AggregationBuilder $aggregation
     *
     * @return PaginatorAdapter
    */
    public static function fromRequest(Builder $builder, $perPage = 15, $pageName = 'page', AggregationBuilder $aggregation): self
    {
        return new static(
            $builder,
            $perPage,
            function () use ($pageName) {
                return Paginator::resolveCurrentPage($pageName);
            },
            $aggregation
        );
    }

    /**
     * @param Builder $builder
     * @param int     $perPage
     * @param int     $page
     * @param AggregationBuilder $aggregation
     *
     * @return PaginatorAdapter
    */
    public static function fromParams(Builder $builder, $perPage = 15, $page = 1, AggregationBuilder $aggregation): self
    {
        return new static(
            $builder,
            $perPage,
            function () use ($page) {
                return $page;
            },
            $aggregation
        );
    }

    public function make(): LengthAwarePaginator
    {
        $page = $this->getCurrentPage();

        $this->builder($this->builder)
             ->skip($this->getSkipAmount($this->perPage, $page))
             ->take($this->perPage);

        return $this->convertToLaravelPaginator(
            $this->getDoctrineQuery(),
            $this->perPage,
            $page
        );
    }

    /**
     * @param Builder $builder
     *
     * @return $this
    */
    protected function builder(Builder $builder): self
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * @return Builder
    */
    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    /**
     * @param int $start
     *
     * @return $this
    */
    protected function skip($start): self
    {
        $this->getBuilder()->skip($start);

        return $this;
    }

    /**
     * @param int $perPage
     *
     * @return $this
    */
    protected function take($perPage): self
    {
        $this->getBuilder()->limit($perPage);

        return $this;
    }

    /**
     * @param int $perPage
     * @param int $page
     *
     * @return int
    */
    protected function getSkipAmount($perPage, $page): int
    {
        return ($page - 1) * $perPage;
    }

    /**
     * @return Query
    */
    private function getDoctrineQuery(): Query
    {
        return $this->getBuilder()->getQuery();
    }

    /**
     * @param Query $query
     * @param int   $perPage
     * @param int   $page
     *
     * @return LengthAwarePaginator
    */
    protected function convertToLaravelPaginator(Query $query, $perPage, $page): LengthAwarePaginator
    {
        $results     = iterator_to_array($query->execute());
        $path        = Paginator::resolveCurrentPath();
        return new LengthAwarePaginator(
            $results,
            $this->count(),
            $perPage,
            $page,
            compact('path')
        );
    }

    /**
     * @return int
    */
    protected function getCurrentPage(): int
    {
        $page = call_user_func($this->pageResolver);
        return $page > 0 ? $page : 1;
    }

    /**
     * @return int
    */
    protected function count(): int
    {
        $count = $this->aggregation
                      ->count('counter')
                      ->execute()
                      ->getSingleResult();

        return isset($count['counter']) ? $count['counter'] : 0;
    }
}