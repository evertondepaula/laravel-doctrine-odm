<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Extensions\SoftDeleteable;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

trait SoftDeletes
{
    /**
     * @ODM\Field(type="date", nullable=true)
     * @var DateTime
    */
    protected $deletedAt;

    /**
     * @return DateTime
    */
    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    /**
     * @param DateTime|null $deletedAt
    */
    public function setDeletedAt(?DateTime $deletedAt = null)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * Restore the soft-deleted state
    */
    public function restore(): void
    {
        $this->deletedAt = null;
    }

    /**
     * @return bool
    */
    public function isDeleted(): bool
    {
        return $this->deletedAt && new DateTime('now') >= $this->deletedAt;
    }
}
