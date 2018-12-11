<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Extensions;

use Illuminate\Contracts\Container\Container;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\Common\EventManager;
use Epsoftware\Laravel\Doctrine\Mongo\Extensions\Extension;

class ExtensionManager
{
    /**
     * @var string[]
    */
    protected $extensions = [];

    /**
     * @var array
    */
    protected $bootedExtensions = [];

    /**
     * @var Container
    */
    protected $container;

    /**
     * ExtensionManager constructor.
     * @param Container $container
    */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Boot the extensions
     * @param DocumentManager $registry
    */
    public function boot(DocumentManager $dm)
    {
        foreach ($this->extensions as $extension) {
            $extension = $this->container->make($extension);
            if ($this->notBootedYet($extension)) {
                $this->bootExtension(
                    $extension,
                    $dm,
                    $dm->getEventManager(),
                    $dm->getConfiguration()
                );
            }
        }
    }

    /**
     * @return bool
    */
    public function needsBooting()
    {
        return count($this->extensions) > 0;
    }

    /**
     * @param string $extension
     */
    public function register($extension)
    {
        $this->extensions[] = $extension;
    }

    /**
     * @param Extension              $extension
     * @param DocumentManager        $dm
     * @param EventManager           $evm
     * @param Configuration          $configuration
    */
    protected function bootExtension(Extension $extension, DocumentManager $dm, EventManager $evm, Configuration $configuration)
    {
        $extension->addSubscribers(
            $evm,
            $dm,
            $configuration->getMetadataDriverImpl()->getDefaultDriver()->getReader()
        );

        if (is_array($extension->getFilters())) {
            foreach ($extension->getFilters() as $name => $filter) {
                $configuration->addFilter($name, $filter);
                $dm->getFilterCollection()->enable($name);
            }
        }

        $this->markAsBooted($extension);
    }

    /**
     * @param Extension $extension
     *
     * @return bool
    */
    protected function notBootedYet(Extension $extension)
    {
        return !isset($this->bootedExtensions[get_class($extension)]);
    }

    /**
     * @param           $connection
     * @param Extension $extension
    */
    protected function markAsBooted(Extension $extension)
    {
        $this->bootedExtensions[get_class($extension)] = true;
    }

    /**
     * @return array|Extension[]
    */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * @return array
    */
    public function getBootedExtensions()
    {
        return $this->bootedExtensions;
    }
}
