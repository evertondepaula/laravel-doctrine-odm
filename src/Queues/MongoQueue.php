<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Queues;

use Illuminate\Queue\Queue;
use Illuminate\Support\Carbon;
use Epsoftware\Laravel\Doctrine\Mongo\Queues\Jobs\MongoJob;
use Epsoftware\Laravel\Doctrine\Mongo\Queues\Jobs\MongoJobRecord;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use MongoDB;

class MongoQueue extends Queue implements QueueContract
{
    /**
     * The mongo connection instance.
     *
     * @var \MongoDB
    */
    protected $database;

    /**
     * The mongo collection that holds the jobs.
     *
     * @var string
     */
    protected $collection;

    /**
     * The name of the default queue.
     *
     * @var string
    */
    protected $default;

    /**
     * The expiration time of a job.
     *
     * @var int|null
    */
    protected $retryAfter = 60;

    /**
     * Create a new database queue instance.
     *
     * @param  \MongoDB $database
     * @param  string            $collection
     * @param  string            $default
     * @param  int               $retryAfter
     * @return void
    */
    public function __construct(MongoDB $database, string $collection, $default = 'default', $retryAfter = 60)
    {
        $this->database   = $database;
        $this->collection = $collection;
        $this->default    = $default;
        $this->retryAfter = $retryAfter;
    }

    /**
     * Get the size of the queue.
     *
     * @param string $queue
     * @return int
    */
    public function size($queue = null)
    {
        return $this->database->selectCollection($this->collection)
                    ->count([
                        'queue' => $this->getQueue($queue)
                    ]);
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  string  $job
     * @param  mixed   $data
     * @param  string  $queue
     * @return mixed
    */
    public function push($job, $data = '', $queue = null)
    {
        return $this->pushToDatabase($queue, $this->createPayload(
            $job, $this->getQueue($queue), $data
        ));
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string  $payload
     * @param  string  $queue
     * @param  array   $options
     * @return mixed
    */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        return $this->pushToDatabase($queue, $payload);
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  string  $job
     * @param  mixed   $data
     * @param  string  $queue
     * @return void
    */
    public function later($delay, $job, $data = '', $queue = null)
    {
        return $this->pushToDatabase($queue, $this->createPayload(
            $job, $this->getQueue($queue), $data
        ), $delay);
    }

    /**
     * Push an array of jobs onto the queue.
     *
     * @param  array   $jobs
     * @param  mixed   $data
     * @param  string  $queue
     * @return mixed
    */
    public function bulk($jobs, $data = '', $queue = null)
    {
        $queue = $this->getQueue($queue);
        $availableAt = $this->availableAt();

        $jobs = collect((array) $jobs)->map(
            function ($job) use ($queue, $data, $availableAt) {
                return (object) $this->buildDatabaseRecord($queue, $this->createPayload($job, $this->getQueue($queue), $data), $availableAt);
            }
        )->all();

        return $this->database->selectCollection($this->collection)->insert($jobs);
    }

    /**
     * Release a reserved job back onto the queue.
     *
     * @param  string  $queue
     * @param  \Epsoftware\Laravel\Doctrine\Mongo\Queues\Jobs\MongoJobRecord $job
     * @param  int  $delay
     * @return mixed
    */
    public function release($queue, $job, $delay)
    {
        return $this->pushToDatabase($queue, $job->payload, $delay, $job->attempts);
    }

    /**
     * Push a raw payload to the database with a given delay.
     *
     * @param  string|null  $queue
     * @param  string  $payload
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  int  $attempts
     * @return mixed
    */
    protected function pushToDatabase($queue, $payload, $delay = 0, $attempts = 0)
    {
        $job = (object) $this->buildDatabaseRecord($this->getQueue($queue), $payload, $this->availableAt($delay), $attempts);

        $this->database
                ->selectCollection($this->collection)
                ->insert($job);

        return (string) $job->_id;
    }

    /**
     * Create an array to insert for the given job.
     *
     * @param  string|null  $queue
     * @param  string  $payload
     * @param  int  $availableAt
     * @param  int  $attempts
     * @return array
    */
    protected function buildDatabaseRecord($queue, $payload, $availableAt, $attempts = 0)
    {
        return [
            'queue'       => $queue,
            'attempts'    => $attempts,
            'reservedAt'  => null,
            'availableAt' => $availableAt,
            'createdAt'   => $this->currentTime(),
            'payload'     => $payload,
        ];
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param  string  $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     *
     * @throws \Exception|\Throwable
    */
    public function pop($queue = null)
    {
        $result = null;

        $queue = $this->getQueue($queue);

        $job = $this->getNextAvailableJob($queue);

        if ($job) {
            $result = $this->marshalJob($queue, $job);
        }

        return $result;
    }

    /**
     * Get the next available job for the queue.
     *
     * @param  string|null  $queue
     * @return \Epsoftware\Laravel\Doctrine\Mongo\Queues\Jobs\MongoJobRecord|null
    */
    protected function getNextAvailableJob($queue)
    {
        $job = $this->database->selectCollection($this->collection)
                    ->findAndModify(
                        [
                            'queue' => $this->getQueue($queue),
                            '$or'   => [
                                $this->isAvailable(),
                                $this->isReservedButExpired(),
                            ]
                        ],
                        [
                            '$set' => [
                                'reservedAt' => $this->currentTime(),
                            ],
                            '$inc' => [
                                'attempts' => 1,
                            ]
                        ],
                        null,
                        [
                            '$sort' => [ '_id' => 'ASC' ],
                            'new'   => true
                        ]
                    );

        return $job ? new MongoJobRecord($job) : null;
    }

    /**
     * Modify the query to check for available jobs.
     *
     * @return void
    */
    protected function isAvailable()
    {
        return [
            '$and' => [
                [ 'reservedAt'  => NULL ],
                [ 'availableAt' => [ '$lte' => $this->currentTime() ] ]
            ]
        ];
    }

    /**
     * Modify the query to check for jobs that are reserved but have expired.
     *
     * @return void
    */
    protected function isReservedButExpired()
    {
        $expiration = Carbon::now()->subSeconds($this->retryAfter)->getTimestamp();

        return [
            'reservedAt' => [ '$lte' => $expiration ]
        ];
    }

    /**
     * Marshal the reserved job into a MongoJob instance.
     *
     * @param  string  $queue
     * @param  \Epsoftware\Laravel\Doctrine\Mongo\Queues\Jobs\MongoJobRecord $job
     * @return \Epsoftware\Laravel\Doctrine\Mongo\Queues\Jobs\MongoJob
    */
    protected function marshalJob($queue, $job)
    {
        return new MongoJob(
            $this->container, $this, $job, $this->connectionName, $queue
        );
    }

    /**
     * Delete a reserved job from the queue.
     *
     * @param  string  $queue
     * @param  string  $id
     * @return void
     *
     * @throws \Exception|\Throwable
    */
    public function deleteReserved($queue, $id)
    {
        $this->database
                ->selectCollection($this->collection)
                ->remove(['_id' => new \MongoDB\BSON\ObjectId($id)], ['justOne' => true]);
    }

    /**
     * Get the queue or return the default.
     *
     * @param  string|null  $queue
     * @return string
     */
    public function getQueue($queue)
    {
        return $queue ?: $this->default;
    }

    /**
     * Get the underlying database instance.
     *
     * @return \MongoDB
    */
    public function getDatabase()
    {
        return $this->database;
    }
}
