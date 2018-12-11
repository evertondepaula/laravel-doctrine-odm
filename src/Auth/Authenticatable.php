<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Auth;

trait Authenticatable
{
    /**
     * @ODM\Field(type="string")
    */
    protected $password;

    /**
     * @ODM\Field(type="string", nullable=true)
    */
    protected $rememberToken;

    /**
     * Get the Field name for the primary key
     * @return string
    */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     * @return mixed
    */
    public function getAuthIdentifier()
    {
        $name = $this->getAuthIdentifierName();
        return $this->{$name};
    }

    /**
     * @return string
    */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
    */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get the password for the user.
     * @return string
    */
    public function getAuthPassword()
    {
        return $this->getPassword();
    }

    /**
     * Get the token value for the "remember me" session.
     * @return string
    */
    public function getRememberToken()
    {
        return $this->rememberToken;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     *
     * @return void
    */
    public function setRememberToken($value)
    {
        $this->rememberToken = $value;
    }

    /**
     * Get the Field name for the "remember me" token.
     * @return string
    */
    public function getRememberTokenName()
    {
        return 'rememberToken';
    }

    /**
     * Get the username identifies for blameable.
     *
     * @return string
    */
    public function getUsername()
    {
        return $this->getName();
    }
}
