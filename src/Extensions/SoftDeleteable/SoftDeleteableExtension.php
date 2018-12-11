<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Extensions\SoftDeleteable;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Gedmo\SoftDeleteable\SoftDeleteableListener;
use Gedmo\SoftDeleteable\Filter\ODM\SoftDeleteableFilter;
use Epsoftware\Laravel\Doctrine\Mongo\Extensions\Gedmo\GedmoExtension;

class SoftDeleteableExtension extends GedmoExtension
{
    /**
     * @param EventManager           $manager
     * @param DocumentManager $em
     * @param Reader                 $reader
    */
    public function addSubscribers(EventManager $manager, DocumentManager $dm, Reader $reader = null)
    {
        $subscriber = new SoftDeleteableListener();
        $this->addSubscriber($subscriber, $manager, $reader);
    }

    /**
     * @return array
    */
    public function getFilters()
    {
        return [
            'soft-deleteable' => SoftDeleteableFilter::class
        ];
    }
}
