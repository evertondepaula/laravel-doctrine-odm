<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test;

use Orchestra\Testbench\TestCase as Test;
use Epsoftware\Laravel\Doctrine\Mongo\Facades\Mongo;

class TestCase extends Test
{
    public $database;

    protected function setUp()
    {
        parent::setUp();
        $this->database = env('MONGO_DATABASE', 'db_testing');
    }
    
    protected function tearDown()
    {
        Mongo::getClient()->dropDB($this->database);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return void
    */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('doctrine-mongodb.connection', [
            'host'     => env('MONGO_HOST', '127.0.0.1'),
            'port'     => env('MONGO_PORT', '27017'),
            'database' => env('MONGO_DATABASE', 'test'),
            'username' => env('MONGO_USERNAME', 'root'),
            'password' => env('MONGO_PASSWORD', 'root'),
        ]);

        $app['config']->set('doctrine-mongodb.documents.dirs', [
            __DIR__ . '/Documents'
        ]);

        $app['config']->set('app.locale', 'en_us');
        $app['config']->set('app.fallback_locale', 'en_us');

        $app['config']->set('doctrine-mongodb.filters', [
            'locale' => \Epsoftware\Laravel\Doctrine\Mongo\Test\Filters\LocaleFilter::class
        ]);

        $app['config']->set('doctrine-mongodb.extensions', [
            \Epsoftware\Laravel\Doctrine\Mongo\Extensions\Timestampable\TimestampableExtension::class,
            \Epsoftware\Laravel\Doctrine\Mongo\Extensions\Loggable\LoggableExtension::class,
            \Epsoftware\Laravel\Doctrine\Mongo\Extensions\Blameable\BlameableExtension::class,
            \Epsoftware\Laravel\Doctrine\Mongo\Extensions\Treeable\TreeExtension::class,
            \Epsoftware\Laravel\Doctrine\Mongo\Extensions\Translatable\TranslatableExtension::class,
            \Epsoftware\Laravel\Doctrine\Mongo\Extensions\Sluggable\SluggableExtension::class,
            \Epsoftware\Laravel\Doctrine\Mongo\Extensions\SoftDeleteable\SoftDeleteableExtension::class,
        ]);

        $app['config']->set('auth.providers', [
           'users' => [
                'driver' => 'doctrine',
                'model' => \Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\User::class,
            ],
        ]);

        $app['config']->set('queue.connections', [
           'mongodb' => [
                'driver'      => 'mongodb',
                'database'    => env('MONGO_DATABASE', 'db_testing'),
                'collection'  => 'Jobs',
                'queue'       => 'default',
                'retry_after' => 90,
            ],
        ]);

        $app['config']->set('queue.failed', [
            'driver'     => 'mongodb',
            'collection' => 'FailedJobs',
            'database'   => 'Jobs',
        ]);

    }
    
    protected function getPackageProviders($app)
    {
        return [
            \Epsoftware\Laravel\Doctrine\Mongo\Providers\LaravelDoctrineOdmProvider::class,
            \Epsoftware\Laravel\Doctrine\Mongo\Extensions\Gedmo\GedmoExtensionsServiceProvider::class,
            \Epsoftware\Laravel\Doctrine\Mongo\Queues\MongoQueueProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Auth'            => \Illuminate\Support\Facades\Auth::class,
            'Validator'       => \Illuminate\Support\Facades\Validator::class,
            'Mongo'           => \Epsoftware\Laravel\Doctrine\Mongo\Facades\Mongo::class,
            'DocumentManager' => \Epsoftware\Laravel\Doctrine\Mongo\Facades\DocumentManager::class,
        ];
    }
}