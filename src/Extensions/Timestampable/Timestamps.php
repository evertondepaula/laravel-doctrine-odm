<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Extensions\Timestampable;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;

trait Timestamps
{
    /**
     * @ODM\Field(type="date", nullable=false)
     * @Gedmo\Timestampable(on="create")
     * @var DateTime
    */
    protected $createdAt;

    /**
     * @ODM\Field(type="date", nullable=false)
     * @Gedmo\Timestampable(on="update")
     * @var DateTime
    */
    protected $updatedAt;

    /**
     * @return DateTime
    */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return DateTime
    */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $createdAt
    */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param DateTime $updatedAt
    */
    public function setUpdatedAt(DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
