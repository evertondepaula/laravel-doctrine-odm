<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Facades;

use Illuminate\Support\Facades\Facade;

class Mongo extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'doctrine-mongo'; }
}