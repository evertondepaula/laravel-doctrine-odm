<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test\Filters;

use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Filter\BsonFilter;

class LocaleFilter extends BsonFilter
{
    public function addFilterCriteria(ClassMetadata $targetDocument): array
    {
        return [
            'locale' => $this->getParameter('locale')
        ];
    }
}