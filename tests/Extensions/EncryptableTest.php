<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test\Extensions;

use Epsoftware\Laravel\Doctrine\Mongo\Test\TestCase;
use Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\Extension;
use DocumentManager;

class EncryptableTest extends TestCase
{
    public function testEncryptable()
    {
        $extension = new Extension();
        $extension->setDocument('111.111.111-11');
        DocumentManager::persist($extension);
        DocumentManager::flush();

        $extension = DocumentManager::getRepository(Extension::class)->find($extension->getId());

        $document = $extension->getDocument();

        $this->assertSame('111.111.111-11', $document);
    }
}