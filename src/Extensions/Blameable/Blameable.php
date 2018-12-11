<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Extensions\Blameable;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

trait Blameable
{
    /**
     * @var string $createdBy
     *
     * @ODM\Field(type="string")
     * @Gedmo\Blameable(on="create")
    */
    private $createdBy;

    /**
     * @var string $updatedBy
     *
     * @ODM\Field(type="string")
     * @Gedmo\Blameable
    */
    private $updatedBy;

    /**
     * @return string
    */
    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }
    
    /**
     * @return string
    */
    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }
}
