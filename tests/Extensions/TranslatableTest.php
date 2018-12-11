<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test\Extensions;

use Gedmo\Translatable\Document\Translation;
use Epsoftware\Laravel\Doctrine\Mongo\Test\TestCase;
use Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\Extension;
use DocumentManager;

class TranslatableTest extends TestCase
{
    public function testTranslatable()
    {
        $extension = new Extension();
        $extension->setContent('Some name in en');
        DocumentManager::persist($extension);
        DocumentManager::flush();

        $extension = DocumentManager::getRepository(Extension::class)->find($extension->getId());

        $extension->setContent('Some name in pt');
        $extension->setTranslatableLocale('pt_br');
        DocumentManager::persist($extension);
        DocumentManager::flush();

        $translates = DocumentManager::getRepository(Translation::class)->findTranslations($extension);

        $this->assertArrayHasKey('pt_br', $translates);
    }
}