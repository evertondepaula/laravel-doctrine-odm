<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Extensions\Timestampable;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Gedmo\Timestampable\TimestampableListener;
use Epsoftware\Laravel\Doctrine\Mongo\Extensions\Gedmo\GedmoExtension;

class TimestampableExtension extends GedmoExtension
{
    /**
     * @param EventManager           $manager
     * @param DocumentManager        $dm
     * @param Reader|null            $reader
    */
    public function addSubscribers(EventManager $manager, DocumentManager $dm, Reader $reader = null)
    {
        $subscriber = new TimestampableListener;
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
