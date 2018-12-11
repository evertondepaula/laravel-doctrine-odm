<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Providers;

use Illuminate\Support\ServiceProvider;
use Epsoftware\Laravel\Doctrine\Mongo\Validations\PresenceVerifierProvider;
use Epsoftware\Laravel\Doctrine\Mongo\Exceptions\ExtensionNotFound;
use Epsoftware\Laravel\Doctrine\Mongo\Extensions\ExtensionManager;
use Epsoftware\Laravel\Doctrine\Mongo\Auth\DoctrineUserProvider;
use InvalidArgumentException;

class LaravelDoctrineOdmProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/doctrine-mongodb.php', 'doctrine-mongodb'
        );

        $this->app->singleton('doctrine-mongo', function ($app) {
            return new \Epsoftware\Laravel\Doctrine\Mongo\Mongo($app->config['doctrine-mongodb']);
        });

        $this->app->singleton('doctrine-document-manager', function ($app) {
            return \Epsoftware\Laravel\Doctrine\Mongo\Facades\Mongo::getDocumentManager();
        });

        if ($this->shouldRegisterDoctrinePresenceValidator()) {
            $this->registerPresenceVerifierProvider();
        }

        $this->app->afterResolving('doctrine-mongo', function () {
            $this->bootExtensionManager();
        });
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
    */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/doctrine-mongodb.php' => config_path('doctrine-mongodb.php')
        ], 'config');

        $this->extendAuthManager();
        $this->ensureValidatorIsUsable();
        $this->registerExtensions();
    }

    /**
     * Extend the auth manager
    */
    protected function extendAuthManager()
    {
        if ($this->app->bound('auth')) {
            $this->app->make('auth')->provider('doctrine', function ($app, $config) {
                $entity = $config['model'];
                $dm = $app['doctrine-document-manager'];

                if (!$dm) {
                    throw new InvalidArgumentException("No EntityManager is set-up for {$entity}");
                }

                return new DoctrineUserProvider(
                    $app['hash'],
                    $dm,
                    $entity
                );
            });
        }
    }

    /**
     * Register the deferred service provider for the validation presence verifier
    */
    protected function registerPresenceVerifierProvider()
    {
        if ($this->isLumen()) {
            $this->app->singleton('validator', function () {
                $this->app->register(PresenceVerifierProvider::class);
                return $this->app->make('validator');
            });
        } else {
            $this->app->register(PresenceVerifierProvider::class);
        }
    }

    protected function ensureValidatorIsUsable()
    {
        if (!$this->isLumen()) {
            return;
        }

        if ($this->shouldRegisterDoctrinePresenceValidator()) {
            unset($this->app->availableBindings['validator']);
            unset($this->app->availableBindings['Illuminate\Contracts\Validation\Factory']);
        } else {
            $this->app['db'];
        }
    }

    /**
     * Register doctrine extensions
    */
    protected function registerExtensions()
    {
        $this->app->singleton(ExtensionManager::class, function ($app) {

            $manager = new ExtensionManager($app);

            foreach ($this->app->make('config')->get('doctrine-mongodb.extensions', []) as $extension) {

                if (!class_exists($extension)) {
                    throw new ExtensionNotFound("Extension {$extension} not found");
                }

                $manager->register($extension);
            }

            return $manager;
        });
    }

    /**
     * Boots the extension manager at the appropriate time depending on if the app
     * is running as Laravel HTTP, Lumen HTTP or in a console environment
    */
    protected function bootExtensionManager()
    {
        $manager = $this->app->make(ExtensionManager::class);

        if ($manager->needsBooting()) {

            $this->app['events']->fire('doctrine.extensions.booting');

            $this->app->make(ExtensionManager::class)->boot(
                $this->app['doctrine-document-manager']
            );

            $this->app['events']->fire('doctrine.extensions.booted');
        }
    }

    /**
     * @return bool
    */
    protected function shouldRegisterDoctrinePresenceValidator()
    {
        return $this->app['config']->get('doctrine-mongodb.php.doctrine_presence_verifier', true);
    }

    /**
     * @return bool
    */
    protected function isLumen()
    {
        return str_contains($this->app->version(), 'Lumen');
    }
}
