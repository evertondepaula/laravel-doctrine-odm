<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Extensions;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ODM\MongoDB\DocumentManager;

interface Extension
{
    /**
     * @param EventManager    $manager
     * @param DocumentManager $dm
     * @param Reader|null     $reader
    */
    public function addSubscribers(EventManager $manager, DocumentManager $dm, Reader $reader = null);

    /**
     * @return array
    */
    public function getFilters();
}
