<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test\Documents;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Epsoftware\Laravel\Doctrine\Mongo\Extensions\SoftDeleteable\SoftDeletes;
use Epsoftware\Laravel\Doctrine\Mongo\Extensions\Timestampable\Timestamps;
use Epsoftware\Laravel\Doctrine\Mongo\Extensions\Blameable\Blameable;
use DateTime;

/**
 * @ODM\Document
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
*/
class Extension
{
    use Timestamps, SoftDeletes, Blameable;

    /** @ODM\Id */
    private $id;

    /** @ODM\Field(type="string", nullable=true) */
    private $name;

    /** 
     * @Gedmo\Slug(fields={"name"})
     * @ODM\Field(type="string", nullable=true)
    */
    private $slug;

    /**
     * @Gedmo\Translatable
     * @ODM\Field(type="string")
    */
    protected $content;

    /**
     * @Gedmo\Locale
    */
    protected $locale;

    public function getId() { return $this->id; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function getSlug() { return $this->slug; }

    public function getContent() { return $this->content; }
    public function setContent($content) { $this->content = $content; }

    public function setTranslatableLocale($locale) { $this->locale = $locale; }
}