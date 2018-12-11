<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Extensions\Treeable;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;
use Epsoftware\Laravel\Doctrine\Mongo\Extensions\Treeable\MaterializedPath;
use DateTime;

/**
 * @ODM\MappedSuperclass(repositoryClass="Gedmo\Tree\Document\MongoDB\Repository\MaterializedPathRepository")
 * @Gedmo\Tree(type="materializedPath", activateLocking=true)
 */
abstract class NestedSet implements MaterializedPath
{
    /**
     * @ODM\Id
    */
    private $id;

    /**
     * @ODM\Field(type="string")
     * @Gedmo\TreePathSource
    */
    private $title;

    /**
     * @ODM\Field(type="string")
     * @Gedmo\TreePath(separator=",")
    */
    private $path;

    /**
     * @Gedmo\TreeParent
     * @ODM\ReferenceOne(targetDocument="\Epsoftware\Laravel\Doctrine\Mongo\Extensions\Treeable\NestedSet", storeAs="dbRefWithDb")
    */
    private $parent;

    /**
     * @Gedmo\TreeLevel
     * @ODM\Field(type="int")
     */
    private $level;

    /**
     * @Gedmo\TreeLockTime
     * @ODM\Field(type="date", nullable=true)
    */
    private $lockTime;

    /**
     * @return string
    */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string $title
     * @return self
    */
    public function setTitle(?string $title): MaterializedPath
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
    */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param MaterializedPath
     * @return self
    */
    public function setParent(?MaterializedPath $parent = null): MaterializedPath
    {
        $this->parent = $parent;

        return $this;
    }
    
    /**
     * @return MaterializedPath
    */
    public function getParent(): ?MaterializedPath
    {
        return $this->parent;
    }

    /**
     * @return int
    */
    public function getLevel(): ?int
    {
        return $this->level;
    }

    /**
     * @return string
    */
    public function getPath(): ?string
    {
        return $this->path;
    }
    
    /**
     * @return DateTime
    */
    public function getLockTime(): ?DateTime
    {
        return $this->lockTime;
    }
}