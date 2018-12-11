<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Validations;

use Illuminate\Validation\PresenceVerifierInterface;
use Doctrine\ODM\MongoDB\Aggregation\Stage\Match;
use Doctrine\ODM\MongoDB\Aggregation\Builder;
use Doctrine\ODM\MongoDB\DocumentManager;
use InvalidArgumentException;

class DoctrinePresenceVerifier implements PresenceVerifierInterface
{
    /**
     * @var DocumentManager
    */
    protected $dm;

    /**
     * @param DocumentManager $dm
    */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * Count the number of objects in a collection having the given value.
     *
     * @param string $collection
     * @param string $column
     * @param string $value
     * @param int    $excludeId
     * @param string $idColumn
     * @param array  $extra
     *
     * @return int
    */
    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = [])
    {
        $builder = $this->select($collection);

        $builder = $builder->match()
                    ->field($column)
                    ->equals($value);

        if (!is_null($excludeId) && $excludeId != 'NULL') {
            $idColumn = $idColumn ?: 'id';
            $builder = $builder->field($idColumn)
                               ->notEqual($excludeId);
        }

        $builder = $this->queryExtraConditions($extra, $builder);

        $result = $builder
                    ->count('count')
                    ->execute()
                    ->current();

        return isset($result['count']) ? $result['count'] : 0;
    }

    /**
     * Count the number of objects in a collection with the given values.
     *
     * @param string $collection
     * @param string $column
     * @param array  $values
     * @param array  $extra
     *
     * @return int
     */
    public function getMultiCount($collection, $column, array $values, array $extra = [])
    {
        $builder = $this->select($collection);

        $builder = $builder->match()
                            ->field($column)
                            ->in($values);

        $builder = $this->queryExtraConditions($extra, $builder);

        $result = $builder
                    ->count('count')
                    ->execute()
                    ->current();

        return isset($result['count']) ? $result['count'] : 0;
    }
    /**
     * @param string $collection
     *
     * @return \Doctrine\ODM\MongoDB\Aggregation\Builder
     */
    protected function select($collection)
    {
        $builder = $this->dm->createAggregationBuilder($collection);

        return $builder;
    }

    /**
     * @param array        $extra
     * @param \Doctrine\ODM\MongoDB\Aggregation\Stage\Match $builder
    */
    protected function queryExtraConditions(array $extra, Match $builder): Match
    {
        foreach ($extra as $key => $extraValue) {
            if ($extraValue === 'NULL') {
                $builder = $builder->field($key)
                                    ->equals(NULL);
            } elseif ($extraValue === 'NOT_NULL') {
                $builder = $builder->field($key)
                                    ->notEqual(NULL);
            } elseif (\Illuminate\Support\Str::startsWith($extraValue, '!')) {
                $builder = $builder->field($key)
                                    ->notEqual(mb_substr($extraValue, 1));
            } else {
                $builder = $builder->field($key)
                                    ->equals($extraValue);
            }
        }

        return $builder;
    }

    /**
     * Set the connection to be used.
     *
     * @param string $connection
     *
     * @return void
    */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }
}
