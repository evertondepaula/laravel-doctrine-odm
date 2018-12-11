<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Queues;

use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Failed\NullFailedJobProvider;
use Illuminate\Queue\Failed\DatabaseFailedJobProvider;
use Epsoftware\Laravel\Doctrine\Mongo\Queues\Failed\MongoFailedJobProvider;
use Epsoftware\Laravel\Doctrine\Mongo\Queues\Connector;
use Epsoftware\Laravel\Doctrine\Mongo\Mongo;

class MongoQueueProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
    */
    protected $defer = false;

    /**
     * Add the connector to the queue drivers.
     *
     * @return void
    */
    public function boot()
    {
        $this->registerConnector($this->app['queue']);
        $this->registerFailedJobServices();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Register the Async queue connector.
     *
     * @param \Illuminate\Queue\QueueManager $manager
     *
     * @return void
    */
    protected function registerConnector($manager)
    {
        $manager->addConnector('mongodb', function () {
            return new Connector($this->app['doctrine-mongo']);
        });
    }

    /**
     * Register the failed job services.
     *
     * @return void
    */
    protected function registerFailedJobServices()
    {
        $this->app->singleton('queue.failer', function () {

            $config = $this->app['config']['queue.failed'];
            
            if (isset($config['driver']) && $config['driver'] === 'mongodb') {
                return new MongoFailedJobProvider(
                    $this->app['doctrine-mongo'], $config['database'], $config['collection']
                );
            }

            return isset($config['table'])
                        ? $this->databaseFailedJobProvider($config)
                        : new NullFailedJobProvider;
        });
    }

    /**
     * Create a new database failed job provider.
     *
     * @param  array  $config
     * @return \Illuminate\Queue\Failed\DatabaseFailedJobProvider
    */
    protected function databaseFailedJobProvider($config)
    {
        return new DatabaseFailedJobProvider(
            $this->app['db'], $config['database'], $config['table']
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
    */
    public function provides()
    {
        return [];
    }
}