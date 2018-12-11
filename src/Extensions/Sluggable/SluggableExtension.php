<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Extensions\Sluggable;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Gedmo\Sluggable\SluggableListener;
use Epsoftware\Laravel\Doctrine\Mongo\Extensions\Gedmo\GedmoExtension;

class SluggableExtension extends GedmoExtension
{
    /**
     * @param EventManager           $manager
     * @param DocumentManager $dm
     * @param Reader                 $reader
    */
    public function addSubscribers(EventManager $manager, DocumentManager $dm, Reader $reader = null)
    {
        $subscriber = new SluggableListener;
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
