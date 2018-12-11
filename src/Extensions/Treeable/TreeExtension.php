<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Extensions\Treeable;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Gedmo\Tree\TreeListener;
use Epsoftware\Laravel\Doctrine\Mongo\Extensions\Gedmo\GedmoExtension;

class TreeExtension extends GedmoExtension
{
    /**
     * @param EventManager    $manager
     * @param DocumentManager $dm
     * @param Reader          $reader
    */
    public function addSubscribers(EventManager $manager, DocumentManager $dm, Reader $reader = null)
    {
        $subscriber = new TreeListener;
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