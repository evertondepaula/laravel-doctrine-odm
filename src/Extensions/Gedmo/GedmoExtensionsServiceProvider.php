<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Extensions\Gedmo;

use Illuminate\Support\ServiceProvider;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Gedmo\DoctrineExtensions;


class GedmoExtensionsServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     * @return void
    */
    public function register()
    {
        $this->app['events']->listen('doctrine.extensions.booting', function () {

            $manager = $this->app->make('doctrine-document-manager');

            $chain = $manager->getConfiguration()->getMetadataDriverImpl();

            if ($this->hasAnnotationReader($chain)) {
                $this->registerGedmoForAnnotations($chain);
            }

        });
    }

    /**
     * @param MappingDriver $driver
     * @return bool
    */
    private function hasAnnotationReader(MappingDriver $driver)
    {
        foreach ($driver->getDrivers() as $driver) {
            if ($driver instanceof AnnotationDriver) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $chain
    */
    private function registerGedmoForAnnotations(MappingDriver $chain)
    {
        if ($this->needsAllMappings()) {
            DoctrineExtensions::registerMappingIntoDriverChainMongodbODM(
                $chain,
                $chain->getDefaultDriver()->getReader()
            );
        } else {
            DoctrineExtensions::registerAbstractMappingIntoDriverChainMongodbODM(
                $chain,
                $chain->getDefaultDriver()->getReader()
            );
        }
    }

    /**
     * @return mixed
    */
    private function needsAllMappings()
    {
        return $this->app->make('config')->get('doctrine-mongodb.gedmo.all_mappings', false) === true;
    }
}
