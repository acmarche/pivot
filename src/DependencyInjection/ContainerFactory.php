<?php

namespace AcMarche\Pivot\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

//https://medium.com/getaway-group-tech-blog/introducing-the-symfony-dependency-injection-container-in-legacy-code-7ea0c2508980
class ContainerFactory
{
    private static ?ContainerInterface $container = null;
    private static string $CACHE_DIR = __DIR__.'/../../../../../var/cache';
    private static string $CACHE_FILE = '/container_cached.php';

    public static function getContainer(): ContainerInterface
    {
        if (self::$container === null) {
            self::$container = self::loadContainer();
        }

        return self::$container;
    }

    private static function loadContainer(): ContainerInterface
    {
        // Load cached container if available
        if (file_exists(self::$CACHE_DIR.self::$CACHE_FILE)) {
            require_once self::$CACHE_DIR.self::$CACHE_FILE;

            return new \ProjectServiceContainer();
        }

        // Otherwise, build and cache a new container
        return self::buildAndCacheContainer();
    }

    private static function cacheContainer(ContainerBuilder $container): void
    {
        // Ensure cache directory exists and is secured
        if (!is_dir(self::$CACHE_DIR) && !mkdir(self::$CACHE_DIR, 0700, true) && !is_dir(self::$CACHE_DIR)) {
            throw new \RuntimeException('Failed to create cache directory');
        }

        // Dump compiled container to cache file
        $dumper = new PhpDumper($container);
        file_put_contents(self::$CACHE_DIR . self::$CACHE_FILE, $dumper->dump());
    }

    public static function getDatabaseConfig(): array
    {
        // Replace placeholders with ENV variables or a secure secret manager
        return [
            'db' => 'DATABASE_NAME',
            'dsn' => 'DATABASE_DSN',
            'username' => 'DATABASE_USER',
            'password' => 'DATABASE_PASSWORD'
        ];
    }

    private static function buildAndCacheContainer(): ContainerInterface
    {
        $container = new ContainerBuilder();

        // Register services, avoiding direct storage of sensitive data
        $container
            ->register('database_config', 'array')
            ->setFactory([self::class, 'getDatabaseConfig']);

        // Load service configurations
        $configPath = realpath(__DIR__.'/../../config');

        if (!$configPath) {
            throw new \RuntimeException('Config path not found');
        }
        $loader = new PhpFileLoader($container, new FileLocator($configPath));
        $loader->load('services.php');

        // Compile and cache the container
        $container->compile();
        self::cacheContainer($container);

        return $container;
    }
}