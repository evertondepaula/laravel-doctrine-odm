<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test\Documents;

use Illuminate\Contracts\Auth\Authenticatable;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Epsoftware\Laravel\Doctrine\Mongo\Auth\Authenticatable as DoctrineAuthenticatable;
use DateTime;

/** @ODM\Document(repositoryClass="\Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\UserRepository") */
class User implements Authenticatable
{
    use DoctrineAuthenticatable;

    /** @ODM\Id */
    private $id;

    /** @ODM\Field(type="int", strategy="increment") */
    private $changes = 0;

    /** @ODM\Field(type="collection") */
    private $notes = array();

    /** @ODM\Field(type="string") */
    private $name;

    /** @ODM\Field(type="int") */
    private $salary;

    /** @ODM\Field(type="date") */
    private $started;

    /**
      * @ODM\Field(type="string")
    */
    private $locale;

    public function getId() { return $this->id; }

    public function getChanges() { return $this->changes; }
    public function incrementChanges() { $this->changes++; }

    public function getNotes() { return $this->notes; }
    public function addNote($note) { $this->notes[] = $note; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function getSalary() { return $this->salary; }
    public function setSalary($salary) { $this->salary = (int) $salary; }

    public function getStarted() { return $this->started; }
    public function setStarted(DateTime $started) { $this->started = $started; }

    public function getLocale() { return $this->locale; }
    public function setLocale($locale) { $this->locale = $locale; }
}