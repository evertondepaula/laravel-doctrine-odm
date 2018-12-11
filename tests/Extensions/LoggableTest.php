<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test\Extensions;

use Epsoftware\Laravel\Doctrine\Mongo\Test\TestCase;
use Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\Extension;
use DocumentManager;
use Mongo;

class LoggableTest extends TestCase
{
    public function testLoggable()
    {
        $extension = new Extension();
        $extension->setName('Some name');

        DocumentManager::persist($extension);
        DocumentManager::flush();

        $count = Mongo::getClient()->selectCollection($this->database, 'LogEntry')->count();

        $this->assertGreaterThanOrEqual($count, 1);
    }
}