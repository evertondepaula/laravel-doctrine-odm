<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test\Queues;

use Illuminate\Foundation\Bus\PendingDispatch;
use Epsoftware\Laravel\Doctrine\Mongo\Test\TestCase;
use Epsoftware\Laravel\Doctrine\Mongo\Test\Jobs\Job;
use Mongo;

class QueueTest extends TestCase
{
    public function testQueueJobs()
    {
        $job = Job::dispatch('Some text')->onConnection('mongodb');
     
        $this->assertInstanceOf(PendingDispatch::class, $job);
    }
}