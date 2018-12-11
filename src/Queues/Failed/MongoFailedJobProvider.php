<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Queues\Failed;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Queue\Failed\FailedJobProviderInterface;
use Epsoftware\Laravel\Doctrine\Mongo\Mongo;
use DateTime;

class MongoFailedJobProvider implements FailedJobProviderInterface
{
    /**
     * The connection resolver implementation.
     *
     * @var Mongo
    */
    protected $connection;

    /**
     * The database connection name.
     *
     * @var string
    */
    protected $database;

    /**
     * The database table.
     *
     * @var string
    */
    protected $collection;

    /**
     * Create a new database failed job provider.
     *
     * @param  Mongo    $connection
     * @param  string   $database
     * @param  string   $collection
     * @return void
    */
    public function __construct(Mongo $connection, $database, $collection)
    {
        $this->connection = $connection;
        $this->database   = $database;
        $this->collection = $collection;
    }

    /**
     * Log a failed job into storage.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  string  $payload
     * @param  \Exception  $exception
     * @return int|null
    */
    public function log($connection, $queue, $payload, $exception)
    {
        $failedAt = (new DateTime())->format('Y-m-d H:i:s');
        $exception = (string) $exception;

        $job = (object) compact('connection', 'queue', 'payload', 'exception', 'failedAt');

        $this->getCollection()
             ->insert($job);

        return (string) $job->_id;
    }

    /**
     * Get a list of all of the failed jobs.
     *
     * @return array
     */
    public function all()
    {
        $faileds = $this->getCollection()
                        ->find()
                        ->sort(['_id' => -1 ]);

        return collect(iterator_to_array($faileds))->transform(function ($job, $key) {
            $job['id'] = (string) $job['_id'];
            return $job;
        })->all();
    }

    /**
     * Get a single failed job.
     *
     * @param  mixed  $id
     * @return object|null
    */
    public function find($id)
    {
        $failed = $this->getCollection()
                        ->findOne(['_id' => new \MongoDB\BSON\ObjectId((string) $id)]);

        return (object) $failed;
    }

    /**
     * Delete a single failed job from storage.
     *
     * @param  mixed  $id
     * @return bool
    */
    public function forget($id)
    {
        $data = $this->getCollection()
                    ->remove(['_id' => new \MongoDB\BSON\ObjectId($id)], ['justOne' => true]);

        return (isset($data['n']) && $data['n'] >= 1);
    }

    /**
     * Flush all of the failed jobs from storage.
     *
     * @return void
    */
    public function flush()
    {
        $this->getCollection()->remove();
    }

    /**
     * Get a mongo collection instance for the collection.
     *
     * @return \MongoCollection
    */
    protected function getCollection(): \MongoCollection
    {
        return $this->connection
                    ->getDatabase($this->database)
                    ->selectCollection($this->collection);
    }
}
