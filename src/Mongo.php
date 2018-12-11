<?php

namespace Epsoftware\Laravel\Doctrine\Mongo;

use Epsoftware\Laravel\Doctrine\Mongo\Helpers\Mapper;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Common\Annotations\AnnotationRegistry;
use MongoDB\Client;

class Mongo
{
    /**
     * @var DocumentManager
    */
    private $dm;

    /**
     * @var Client
    */
    private $client;

    /**
     * @var Configuration
    */
    private $configuration;

    /**
     * @var string
    */
    private $defaultDatabase;

    public function __construct(array $doctrineConfigs)
    {
        $this->registerLoader()
             ->setConfiguration($doctrineConfigs)
             ->setFilters($doctrineConfigs)
             ->setClient($doctrineConfigs)
             ->setDefaultDataBase($doctrineConfigs)
             ->setDocumentManager();
    }

    private function registerLoader(): self
    {
        AnnotationRegistry::registerLoader('class_exists');

        return $this;
    }

    private function setConfiguration(array $doctrineConfigs): self
    {
        $this->configuration = new Configuration();
        $this->configuration->setProxyDir($doctrineConfigs['proxie']['dir']);
        $this->configuration->setProxyNamespace($doctrineConfigs['proxie']['namespace']);
        $this->configuration->setAutoGenerateProxyClasses($doctrineConfigs['hydrators']['auto_generate']);
        $this->configuration->setHydratorDir($doctrineConfigs['hydrators']['dir']);
        $this->configuration->setHydratorNamespace($doctrineConfigs['hydrators']['namespace']);
        $this->configuration->setAutoGenerateHydratorClasses($doctrineConfigs['hydrators']['auto_generate']);

        $this->configuration->setMetadataDriverImpl(
            $this->getMappingDriverChain($doctrineConfigs['documents'])
        );

        return $this;
    }

    private function setFilters(array $doctrineConfigs): self
    {
        $filters = [];

        if (isset($doctrineConfigs['filters'])) {
            $filters = $doctrineConfigs['filters'];
        }

        foreach ($filters as $key => $filter) {
            $this->configuration->addFilter($key, $filter);
        }

        return $this;
    }

    private function setClient(array $doctrineConfigs): self
    {
        $connection = $doctrineConfigs['mongodb'];

        $baseUri = 'mongodb://';

        $connections[] = sprintf('%s:%s', $connection['host'], $connection['port']);

        if (is_array($connection['replicas']) && !empty($connection['replicas'])) {
            foreach ($connection['replicas'] as $replica) {
                $connections[] = sprintf('%s:%s', $replica['host'], $replica['port']);
            }
        }

        $authDb = $connection['auth_db'];

        $uri = sprintf('%s%s/%s', $baseUri, implode(',', $connections), $authDb);

        $options = [
            'username' => $connection['username'],
            'password' => $connection['password'],
        ];

        $map = [
            'typeMap' => [
                'root'     => 'array',
                'document' => 'array'
            ],
        ];

        $this->client = new Client($uri, $options, $map);

        return $this;
    }

    private function setDefaultDataBase(array $doctrineConfigs): self
    {
        if (!empty($doctrineConfigs['mongodb']['database'])) {
            $database = $doctrineConfigs['mongodb']['database'];
            $this->configuration->setDefaultDB($database);
            $this->defaultDatabase = $database;
        }

        return $this;
    }

    private function setDocumentManager(): self
    {
        $this->dm = DocumentManager::create($this->client, $this->configuration);

        return $this;
    }
    
    private function getMappingDriverChain(array $documents): MappingDriver
    {
        $maps = [];

        foreach ($documents['dirs'] as $dir) {
            $dirs = Mapper::listAllFolders($dir);
            $maps = array_merge($maps, $dirs);
        }

        $driver = AnnotationDriver::create($maps);

        $chain = new MappingDriverChain();
        $chain->setDefaultDriver($driver);
        $chain->addDriver($driver, 'annotations');

        return $chain;
    }

    public function setDatabase(string $database): self
    {
        $this->configuration->setDefaultDB($database);
        $this->defaultDatabase = $database;

        $this->dm = DocumentManager::create($this->client, $this->configuration);

        return $this;
    }

    public function getDocumentManager(): DocumentManager
    {
        return $this->dm;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function getDatabase(?string $database = null): \MongoDB\Database
    {
        return $this->client->selectDatabase($database ?? $this->defaultDatabase);
    }
}
