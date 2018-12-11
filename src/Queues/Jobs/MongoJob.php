<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Queues\Jobs;

use Illuminate\Queue\Jobs\Job;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Epsoftware\Laravel\Doctrine\Mongo\Queues\MongoQueue;
use Epsoftware\Laravel\Doctrine\Mongo\Queues\Jobs\MongoJobRecord;

class MongoJob extends Job implements JobContract
{
    /**
     * The database queue instance.
     *
     * @var \Epsoftware\Laravel\Doctrine\Mongo\Queues\MongoQueue
     */
    protected $database;

    /**
     * The database job payload.
     *
     * @var \Epsoftware\Laravel\Doctrine\Mongo\Queues\Jobs\MongoJobRecord
     */
    protected $job;

    /**
     * Create a new job instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @param  \Illuminate\Queue\DatabaseQueue  $database
     * @param  \Epsoftware\Laravel\Doctrine\Mongo\Queues\Jobs\MongoJobRecord  $job
     * @param  string  $connectionName
     * @param  string  $queue
     * @return void
     */
    public function __construct(Container $container, MongoQueue $database, MongoJobRecord $job, string $connectionName, string $queue)
    {
        $this->job = $job;
        $this->queue = $queue;
        $this->database = $database;
        $this->container = $container;
        $this->connectionName = $connectionName;
    }

    /**
     * Release the job back into the queue.
     *
     * @param  int  $delay
     * @return mixed
    */
    public function release($delay = 0)
    {
        parent::release($delay);

        $this->delete();

        return $this->database->release($this->queue, $this->job, $delay);
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
    */
    public function delete()
    {
        parent::delete();

        $this->database->deleteReserved($this->queue, $this->job->_id);
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return (int) $this->job->attempts;
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId()
    {
        return $this->job->_id;
    }

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->job->payload;
    }
}
