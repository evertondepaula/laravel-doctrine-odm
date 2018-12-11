<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test\Extensions;

use Epsoftware\Laravel\Doctrine\Mongo\Test\TestCase;
use Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\Category;
use DocumentManager;

class TreeableTest extends TestCase
{
    public function testTreeable()
    {
        $food = new Category();
        $food->setTitle('Food');

        DocumentManager::persist($food);
        DocumentManager::flush();

        $this->assertEquals($food->getPath(), "Food-{$food->getId()},");

        $fruits = new Category();
        $fruits->setTitle('Fruits');
        $fruits->setParent($food);

        DocumentManager::persist($fruits);
        DocumentManager::flush();

        $this->assertEquals($fruits->getPath(), "Food-{$food->getId()},Fruits-{$fruits->getId()},");
    }
}