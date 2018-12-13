<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Extensions\Encryptable;

use Illuminate\Support\Facades\App;
use Illuminate\Contracts\Encryption\Encrypter;
use Doctrine\Common\EventManager;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ODM\MongoDB\DocumentManager;
use Epsoftware\Doctrine\ODM\Encrypt\Encryptors\LaravelEncryptor;
use Epsoftware\Doctrine\ODM\Encrypt\Subscribers\DoctrineEncryptSubscriber;
use Epsoftware\Laravel\Doctrine\Mongo\Extensions\Extension as ExtensionContract;

class EncryptExtension implements ExtensionContract
{
    /**
     * @param EventManager           $manager
     * @param DocumentManager $dm
     * @param Reader                 $reader
    */
    public function addSubscribers(EventManager $manager, DocumentManager $dm, Reader $reader = null)
    {
        $encrypter = App::make(Encrypter::class);

        $subscriber = new DoctrineEncryptSubscriber(new AnnotationReader, new LaravelEncryptor($encrypter));

        $manager->addEventSubscriber($subscriber);
    }

    /**
     * @return array
    */
    public function getFilters()
    {
        return [];
    }
}
