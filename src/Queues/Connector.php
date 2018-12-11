<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Queues;

use Illuminate\Queue\Connectors\ConnectorInterface;
use Epsoftware\Laravel\Doctrine\Mongo\Queues\MongoQueue;
use Epsoftware\Laravel\Doctrine\Mongo\Mongo;

class Connector implements ConnectorInterface
{
    /**
     * @var Mongo
    */
    protected $connection;
    
    /**
     * @param Mongo $connection
    */
    public function __construct(Mongo $connection)
    {
        $this->connection = $connection;
    }
    
    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return \Illuminate\Contracts\Queue\Queue
    */
    public function connect(array $config)
    {
        return new MongoQueue(
            $this->connection->getDatabase($config['database']??null),
            $config['collection'],
            $config['queue'],
            $config['retry_after'] ?? 90
        );
    }
}