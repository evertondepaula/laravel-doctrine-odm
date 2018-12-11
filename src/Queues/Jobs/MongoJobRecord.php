<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Queues\Jobs;

use Illuminate\Support\InteractsWithTime;

class MongoJobRecord
{
    use InteractsWithTime;

    /**
     * The underlying job record.
     *
     * @var \stdClass
    */
    protected $record;

    /**
     * Create a new job record instance.
     *
     * @param  array $record
     * @return void
    */
    public function __construct(array $record)
    {
        $this->record = (object) $record;
    }

    /**
     * Increment the number of times the job has been attempted.
     *
     * @return int
    */
    public function increment()
    {
        $this->record->attempts++;

        return $this->record->attempts;
    }

    /**
     * Update the "reserved at" timestamp of the job.
     *
     * @return int
    */
    public function touch()
    {
        $this->record->reservedAt = $this->currentTime();

        return $this->record->reservedAt;
    }

    /**
     * Dynamically access the underlying job information.
     *
     * @param  string  $key
     * @return mixed
    */
    public function __get($key)
    {
        return $this->record->{$key};
    }
}