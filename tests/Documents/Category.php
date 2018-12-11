<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test\Documents;

use Epsoftware\Laravel\Doctrine\Mongo\Extensions\Treeable\NestedSet;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
*/
class Category extends NestedSet
{
}