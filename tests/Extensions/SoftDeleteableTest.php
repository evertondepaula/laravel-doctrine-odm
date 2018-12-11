<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test\Extensions;

use Epsoftware\Laravel\Doctrine\Mongo\Test\TestCase;
use Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\Extension;
use DocumentManager;
use DateTime;

class SoftDeleteableTest extends TestCase
{
    public function testSoftDeleted()
    {
        $extension = new Extension();
        $extension->setName('Some name');

        DocumentManager::persist($extension);
        DocumentManager::flush();

        $id = $extension->getId();

        DocumentManager::remove($extension);
        DocumentManager::flush();

        $extension = DocumentManager::getRepository(Extension::class)->findOneBy(['id' => $id]);

        $this->assertNull($extension);

        DocumentManager::getFilterCollection()->disable('soft-deleteable');

        $extension = DocumentManager::getRepository(Extension::class)->findOneBy(['id' => $id]);

        $this->assertInstanceOf(Extension::class, $extension);

    }
}