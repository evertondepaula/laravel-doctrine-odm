<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test\Extensions;

use Epsoftware\Laravel\Doctrine\Mongo\Test\TestCase;
use Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\User;
use Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\Extension;
use DocumentManager;
use Mongo;
use Auth;

class BlameableTest extends TestCase
{
    public function testLoggable()
    {
        $user = new User();
        $user->setName('Everton');

        DocumentManager::persist($user);
        DocumentManager::flush();

        Auth::setUser($user);
        
        $extension = new Extension();
        $extension->setName('Some name');

        DocumentManager::persist($extension);
        DocumentManager::flush();

        $extension = DocumentManager::getRepository(Extension::class)->findOneBy(['id' => $extension->getId()]);

        $this->assertEquals($extension->getCreatedBy(), $user->getName());
    }
}