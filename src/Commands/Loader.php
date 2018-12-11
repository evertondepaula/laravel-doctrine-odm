<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Commands;

use Dotenv\Dotenv;

class Loader
{
    const ENVIRONMENT_PATHS = [
        __DIR__. '/../../../../../.env',
        __DIR__. '/../../../../.env',
        __DIR__. '/../../.env',
    ];

    const CONFIG_PATHS = [
        __DIR__. '/../../../../../config/doctrine-mongodb.php',
        __DIR__. '/../../config/doctrine-mongodb.php',
    ];

    const COMMANDS = [
        \Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateDocumentsCommand::class     => false,
        \Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateRepositoriesCommand::class  => false,
        \Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateHydratorsCommand::class     => true,
        \Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateProxiesCommand::class       => true,
        \Doctrine\ODM\MongoDB\Tools\Console\Command\QueryCommand::class                 => true,
        \Doctrine\ODM\MongoDB\Tools\Console\Command\ClearCache\MetadataCommand::class   => true,
        \Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\ValidateCommand::class       => true,
        \Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\CreateCommand::class         => true,
        \Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\DropCommand::class           => true,
        \Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\UpdateCommand::class         => true,
        \Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\ShardCommand::class          => true,
    ];

    public static function getEnvironment(): void
    {
        foreach (static::ENVIRONMENT_PATHS as $environment) {
            if (file_exists($environment)) {
                $dotenv = new Dotenv(str_replace('.env', '', $environment));
                $dotenv->load();
                break;
            }
        }
    }

    public static function getConfigs(): array
    {
        foreach (static::CONFIG_PATHS as $file) {
            if (file_exists($file)) {
                return require_once $file;
            }
        }
    }

    public static function getCommands(): array
    {
        $commands = [];

        foreach (static::COMMANDS as $command => $available) {

            if (!$available) {
                continue;
            }

            $commands[] = new $command;
        }

        return $commands;
    }
}
