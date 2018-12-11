<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Extensions\Loggable;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Gedmo\Loggable\LoggableListener;
use Epsoftware\Laravel\Doctrine\Mongo\Extensions\Gedmo\GedmoExtension;
use Epsoftware\Laravel\Doctrine\Mongo\Extensions\ResolveUserDecorator;

class LoggableExtension extends GedmoExtension
{
    /**
     * @param EventManager           $manager
     * @param DocumentManager $dm
     * @param Reader                 $reader
    */
    public function addSubscribers(EventManager $manager, DocumentManager $dm, Reader $reader = null)
    {
        $subscriber = new ResolveUserDecorator(
            new LoggableListener,
            'setUsername'
        );

        $this->addSubscriber($subscriber, $manager, $reader);
    }

    /**
     * @return array
    */
    public function getFilters()
    {
        return [];
    }
}
