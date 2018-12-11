<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Extensions\Treeable;

use DateTime;

interface MaterializedPath
{
    /**
     * @return string
    */
    public function getId(): ?string;

    /**
     * @param string $title
     * @return MaterializedPath
    */
    public function setTitle(?string $title): \Epsoftware\Laravel\Doctrine\Mongo\Extensions\Treeable\MaterializedPath;

    /**
     * @return string
    */
    public function getTitle(): ?string;

    /**
     * @param MaterializedPath
     * @return self
    */
    public function setParent(?\Epsoftware\Laravel\Doctrine\Mongo\Extensions\Treeable\MaterializedPath $parent = null): \Epsoftware\Laravel\Doctrine\Mongo\Extensions\Treeable\MaterializedPath;

    /**
     * @return MaterializedPath $parent
    */
    public function getParent(): ?\Epsoftware\Laravel\Doctrine\Mongo\Extensions\Treeable\MaterializedPath;

    /**
     * @return int
    */
    public function getLevel(): ?int;

    /**
     * @return string
    */
    public function getPath(): ?string;
    
    /**
     * @return DateTime
    */
    public function getLockTime();
}
