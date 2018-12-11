<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Extensions\Blameable;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Gedmo\Blameable\BlameableListener;
use Epsoftware\Laravel\Doctrine\Mongo\Extensions\Gedmo\GedmoExtension;
use Epsoftware\Laravel\Doctrine\Mongo\Extensions\ResolveUserDecorator;

class BlameableExtension extends GedmoExtension
{
    
    /**
     * @param EventManager    $manager
     * @param DocumentManager $dm
     * @param Reader          $reader
    */
    public function addSubscribers(EventManager $manager, DocumentManager $dm, Reader $reader = null)
    {
        $subscriber = new ResolveUserDecorator(
            new BlameableListener(),
            'setUserValue'
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