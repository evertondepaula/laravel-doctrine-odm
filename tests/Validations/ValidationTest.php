<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test\Validations;

use Epsoftware\Laravel\Doctrine\Mongo\Test\TestCase;
use Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\User;
use DocumentManager;
use Validator;
use DateTime;

class RepositoryTest extends TestCase
{
    public function testValidationExists()
    {
        $user = $this->createUser();

        $validator = Validator::make(['id' => $user->getId()], [
            'id'   => 'exists:\Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\User,_id',
        ]);

        $this->assertFalse($validator->fails());

        $validator = Validator::make(['id' => 'anyiddoesntexists'], [
            'id'   => 'exists:\Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\User,_id',
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testValidationUnique()
    {
        $user = $this->createUser();

        $validator = Validator::make(['id' => $user->getId()], [
            'id'   => 'unique:\Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\User,_id',
        ]);

        $this->assertTrue($validator->fails());

        $validator = Validator::make(['id' => 'anyuniquevalue'], [
            'id'   => 'unique:\Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\User,_id',
        ]);

        $this->assertFalse($validator->fails());
    }

    private function createUser(): User
    {
        $user = new User();

        $user->setName($this->user['name']);
        $user->setSalary($this->user['salary']);
        $user->setStarted(new DateTime($this->user['started']));
        
        foreach($this->user['notes'] as $note) {
            $user->addNote($note);
        }
        
        DocumentManager::persist($user);
        DocumentManager::flush();

        return $user;
    }

    protected $user = [
        'name'    => 'Test user 1',
        'salary'  => 1,
        'started' => 'now',
        'notes'   => [
            0  => 'Note one',
            1  => 'Note two',
        ],
    ];
}