<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test;

use Epsoftware\Laravel\Doctrine\Mongo\Test\TestCase;
use Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\User;
use DocumentManager;
use DateTime;

class DocumentManagerTest extends TestCase
{
    public function testDocumentManager()
    {
        $user = new User();

        $user->setName('Test user');
        $user->setSalary(1);
        $user->setStarted(new DateTime);
        $user->addNote('Note one');
        $user->addNote('Note two');

        DocumentManager::persist($user);
        DocumentManager::flush();

        $getUser = DocumentManager::getRepository(User::class)->find($user->getId());

        $this->assertTrue($getUser instanceof User);

        DocumentManager::remove($getUser);
        DocumentManager::flush();

        $getUser = DocumentManager::getRepository(User::class)->find($user->getId());

        $this->assertTrue(is_null($getUser));
    }
}