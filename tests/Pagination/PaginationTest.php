<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test\Pagination;

use Illuminate\Pagination\LengthAwarePaginator;
use Epsoftware\Laravel\Doctrine\Mongo\Test\TestCase;
use Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\User;
use DocumentManager;
use DateTime;

class PaginationTest extends TestCase
{
    public function testPaginationAll()
    {
        $this->createUsers();

        $all = DocumentManager::getRepository(User::class)->all();

        $this->assertInstanceOf(LengthAwarePaginator::class, $all);
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