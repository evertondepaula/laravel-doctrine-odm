<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test\Extensions;

use Epsoftware\Laravel\Doctrine\Mongo\Test\TestCase;
use Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\Extension;
use DocumentManager;
use DateTime;

class TimestampableTest extends TestCase
{
    public function testTimestampCreatedAt()
    {
        $extension = new Extension();
        $extension->setName('Some name');

        DocumentManager::persist($extension);
        DocumentManager::flush();

        $extension = DocumentManager::getRepository(Extension::class)->findOneBy(['id' => $extension->getId()]);

        $this->assertInstanceOf(DateTime::class, $extension->getCreatedAt());
    }

    public function testTimestampUpdatedAt()
    {
        $extension = new Extension();
        $extension->setName('Some name');

        DocumentManager::persist($extension);
        DocumentManager::flush();

        $extension = DocumentManager::getRepository(Extension::class)->findOneBy(['id' => $extension->getId()]);

        $this->assertInstanceOf(DateTime::class, $extension->getUpdatedAt());
    }
}