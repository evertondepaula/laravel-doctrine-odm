<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Extensions\Translatable;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Gedmo\Translatable\TranslatableListener;
use Epsoftware\Laravel\Doctrine\Mongo\Extensions\Gedmo\GedmoExtension;

class TranslatableExtension extends GedmoExtension
{
    /**
     * @var Application
    */
    protected $application;

    /**
     * @var Repository
    */
    protected $repository;

    /**
     * @var Dispatcher
    */
    private $events;

    /**
     * @param Application $application
     * @param Repository  $repository
     * @param Dispatcher  $events
    */
    public function __construct(Application $application, Repository $repository, Dispatcher $events)
    {
        $this->application = $application;
        $this->repository  = $repository;
        $this->events      = $events;
    }

    /**
     * @param EventManager    $manager
     * @param DocumentManager $dm
     * @param Reader          $reader
    */
    public function addSubscribers(EventManager $manager, DocumentManager $dm, Reader $reader = null)
    {
        $subscriber = new TranslatableListener;
        $subscriber->setTranslatableLocale($this->application->getLocale());
        $subscriber->setDefaultLocale($this->repository->get('app.locale'));
        $this->addSubscriber($subscriber, $manager, $reader);
        $this->events->listen('locale.changed', function ($locale) use ($subscriber) {
            $subscriber->setTranslatableLocale($locale);
        });
    }

    /**
     * @return array
    */
    public function getFilters()
    {
        return [];
    }
}