<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Extensions\Gedmo;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;
use Epsoftware\Laravel\Doctrine\Mongo\Extensions\Extension as ExtensionContract;

abstract class GedmoExtension implements ExtensionContract
{
    /**
     * @param EventSubscriber $subscriber
     * @param EventManager    $manager
     * @param Reader|null     $reader
    */
    protected function addSubscriber(EventSubscriber $subscriber, EventManager $manager, Reader $reader = null)
    {
        if ($reader) {
            $subscriber->setAnnotationReader($reader);
        }

        $manager->addEventSubscriber($subscriber);
    }
}
