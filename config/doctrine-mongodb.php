<?php

return [

    'mongodb' => [

        'auth_db'    => env('MONGO_AUTH_DATABASE', 'admin'),
        'host'       => env('MONGO_HOST'),
        'port'       => env('MONGO_PORT'),
        'database'   => env('MONGO_DATABASE'),
        'username'   => env('MONGO_USERNAME'),
        'password'   => env('MONGO_PASSWORD'),

        'replicas' => [
            // 0 => [
            //     'host' => '',
            //     'port' => '',
            // ],
        ],
    ],

    'documents' => [
        'dirs' => [
            app_path(). '/Documents',
        ],
    ],

    'filters' => [],

    'extensions' => [
        // \Epsoftware\Laravel\Doctrine\Mongo\Extensions\Timestampable\TimestampableExtension::class,
        // \Epsoftware\Laravel\Doctrine\Mongo\Extensions\Loggable\LoggableExtension::class,
        // \Epsoftware\Laravel\Doctrine\Mongo\Extensions\Blameable\BlameableExtension::class,
        // \Epsoftware\Laravel\Doctrine\Mongo\Extensions\Treeable\TreeExtension::class,
        // \Epsoftware\Laravel\Doctrine\Mongo\Extensions\Translatable\TranslatableExtension::class,
        // \Epsoftware\Laravel\Doctrine\Mongo\Extensions\Sluggable\SluggableExtension::class,
        // \Epsoftware\Laravel\Doctrine\Mongo\Extensions\SoftDeleteable\SoftDeleteableExtension::class,
    ],

    'gedmo' => [
        'all_mappings' => true,
    ],

    'proxie' => [
        'dir'           => resource_path(). '/doctrine/proxies',
        'namespace'     => 'Proxies',
        'auto_generate' => env('MONGO_PROXIE_AUTO_GENERATE', true),
    ],

    'hydrators' => [
        'dir'           => resource_path(). '/doctrine/hydrators',
        'namespace'     => 'Hydrators',
        'auto_generate' => env('MONGO_HYDRATORS_AUTO_GENERATE', true),
    ],

    'doctrine_presence_verifier' => true
];
