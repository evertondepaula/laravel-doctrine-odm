<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test;

use Epsoftware\Laravel\Doctrine\Mongo\Test\TestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Configuration;
use MongoDB\Client;
use Mongo;

class MongoTest extends TestCase
{
    public function testGetDocumentManager()
    {
        $documentManager = Mongo::getDocumentManager();
        
        $this->assertTrue($documentManager instanceof DocumentManager);
    }

    public function testGetClient()
    {
        $client = Mongo::getClient();
        
        $this->assertTrue($client instanceof Client);
    }

    public function testGetConfiguration()
    {
        $configuration = Mongo::getConfiguration();

        $this->assertTrue($configuration instanceof Configuration);
    }

    public function testSetDatabase()
    {
        $dbname = 'other_base';

        Mongo::setDatabase($dbname);

        $configuration = Mongo::getConfiguration();

        $this->assertTrue($configuration->getDefaultDB() === $dbname);
    }
}