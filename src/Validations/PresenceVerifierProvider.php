<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Validations;

use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationServiceProvider;
use Epsoftware\Laravel\Doctrine\Mongo\Validations\DoctrinePresenceVerifier;

class PresenceVerifierProvider extends ValidationServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
    */
    protected $defer = true;

    /**
     * Register the validation factory.
     *
     * @return void
    */
    protected function registerValidationFactory()
    {
        $this->app->singleton('validator', function ($app) {
            $validator = new Factory($app['translator'], $app);
            if (isset($app['doctrine-document-manager']) && isset($app['validation.presence'])) {
                $validator->setPresenceVerifier($app['validation.presence']);
            }
            return $validator;
        });
    }

    /**
     * Register the database presence verifier.
     *
     * @return void
    */
    protected function registerPresenceVerifier()
    {
        $this->app->singleton('validation.presence', function ($app) {
            return new DoctrinePresenceVerifier($app['doctrine-document-manager']);
        });
    }

    /**
     * @return string[]
    */
    public function provides()
    {
        return [
            'validator',
            'validation.presence'
        ];
    }
}