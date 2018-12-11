<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Auth;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;
use ReflectionClass;

class DoctrineUserProvider implements UserProvider
{
    /**
     * @var Hasher
    */
    protected $hasher;

    /**
     * @var EntityManagerInterface
    */
    protected $dm;

    /**
     * @var string
    */
    protected $entity;

    /**
     * @param Hasher                 $hasher
     * @param DocumentManager $dm
     * @param string                 $entity
    */
    public function __construct(Hasher $hasher, DocumentManager $dm, $entity)
    {
        $this->hasher = $hasher;
        $this->entity = $entity;
        $this->dm     = $dm;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier
     *
     * @return Authenticatable|null
    */
    public function retrieveById($identifier)
    {
        return $this->getRepository()->find($identifier);
    }

    /**
     * Retrieve a user by by their unique identifier and "remember me" token.
     *
     * @param mixed  $identifier
     * @param string $token
     *
     * @return Authenticatable|null
    */
    public function retrieveByToken($identifier, $token)
    {
        return $this->getRepository()->findOneBy([
            $this->getEntity()->getAuthIdentifierName() => $identifier,
            $this->getEntity()->getRememberTokenName()  => $token
        ]);
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param Authenticatable $user
     * @param string          $token
     *
     * @return void
    */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
        $this->dm->persist($user);
        $this->dm->flush($user);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     *
     * @return Authenticatable|null
    */
    public function retrieveByCredentials(array $credentials)
    {
        $criteria = [];
        foreach ($credentials as $key => $value) {
            if (!str_contains($key, 'password')) {
                $criteria[$key] = $value;
            }
        }

        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param Authenticatable $user
     * @param array           $credentials
     *
     * @return bool
    */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $this->hasher->check($credentials['password'], $user->getAuthPassword());
    }

    /**
     * Returns repository for the entity.
     * @return DocumentRepository
    */
    protected function getRepository()
    {
        return $this->dm->getRepository($this->entity);
    }

    /**
     * Returns instantiated entity.
     * @return Authenticatable
    */
    protected function getEntity()
    {
        $refEntity = new ReflectionClass($this->entity);
        return $refEntity->newInstanceWithoutConstructor();
    }

    /**
     * Returns entity namespace.
     * @return string
    */
    public function getModel()
    {
        return $this->entity;
    }
}
