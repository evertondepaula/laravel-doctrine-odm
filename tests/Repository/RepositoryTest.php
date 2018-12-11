<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test\Repository;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Epsoftware\Laravel\Doctrine\Mongo\Test\TestCase;
use Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\User;
use DocumentManager;
use DateTime;

class RepositoryTest extends TestCase
{
    public function testRepositoryFindOrFail()
    {
        $this->createUsers();

        $user = DocumentManager::getRepository(User::class)->findOneBy(['name' => 'Test user 1']);

        $this->assertInstanceOf(User::class, $user);

        $findOrFail = DocumentManager::getRepository(User::class)->findOrFail($user->getId());

        $this->assertEquals($findOrFail->getId(), $user->getId());
    }

    public function testRepositoryFindOrFailException()
    {
        $this->createUsers();

        $this->expectException(ModelNotFoundException::class);

        $findOrFail = DocumentManager::getRepository(User::class)->findOrFail('sometext');
    }

    private function createUsers()
    {
        foreach ($this->users as $userParam) {
            $user = new User();

            $user->setName($userParam['name']);
            $user->setSalary($userParam['salary']);
            $user->setStarted(new DateTime($userParam['started']));
            
            foreach($userParam['notes'] as $note) {
                $user->addNote($note);
            }
            
            DocumentManager::persist($user);
        }

        DocumentManager::flush();
    }

    protected $users = [
        0 => [
            'name'    => 'Test user 1',
            'salary'  => 1,
            'started' => 'now',
            'notes'   => [
                0  => 'Note one',
                1  => 'Note two',
            ],
        ],
        1 => [
            'name'    => 'Test user 2',
            'salary'  => 1,
            'started' => 'now',
            'notes'   => [
                0  => 'Note one',
                1  => 'Note two',
            ],
        ],
    ];
}