<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Test\Filters;

use Epsoftware\Laravel\Doctrine\Mongo\Test\TestCase;
use Epsoftware\Laravel\Doctrine\Mongo\Test\Documents\User;
use DocumentManager;
use DateTime;

class FilterTest extends TestCase
{
    protected $locales = ['pt', 'en'];
    
    public function testEnabledFilters()
    {
        $this->createUsersLocales();

        $filter = DocumentManager::getFilterCollection()->enable("locale");
        $filter->setParameter('locale', ['$in' => ['en']]);

        $users = DocumentManager::createQueryBuilder(User::class)
                                ->limit(10)
                                ->skip(0)
                                ->getQuery()
                                ->execute()
                                ->toArray();

        $this->assertCount(1, $users);
    }

    public function testDisableFilters()
    {
        $this->createUsersLocales();

        $filter = DocumentManager::getFilterCollection()->enable("locale");
        $filter->setParameter('locale', ['$in' => ['en']]);

        $filter = DocumentManager::getFilterCollection()->disable("locale");

        $users = DocumentManager::createQueryBuilder(User::class)
                                ->limit(10)
                                ->skip(0)
                                ->getQuery()
                                ->execute()
                                ->toArray();

        $this->assertTrue(count($users) > 1);
    }

    private function createUsersLocales()
    {
        foreach ($this->locales as $locale) {
            $user = new User();
            $user->setName('Test user');
            $user->setSalary(1);
            $user->setStarted(new DateTime);
            $user->addNote('Note one');
            $user->addNote('Note two');
            $user->setLocale($locale);

            DocumentManager::persist($user);
        }

        DocumentManager::flush();
    }


}