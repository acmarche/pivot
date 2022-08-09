<?php

namespace AcMarche\Pivot\DependencyInjection;

use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ErrorHandler\Debug;

class PivotContainer
{
    public function __construct()
    {

    }

    static function init(): ContainerInterface
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            Debug::enable();
            $env = 'dev';
        } else {
            define('WP_DEBUG', false);
            $env = 'prod';
        }

        /**
         * mode hors sf
         */
        $dir = dirname(__DIR__).'/../../../../';
        if ( ! isset($_SERVER['APP_CACHE_DIR'])) {
            $_SERVER['APP_CACHE_DIR'] = $dir.'var/cache';
        }
        if ( ! isset($_SERVER['APP_LOG_DIR'])) {
            $_SERVER['APP_LOG_DIR'] = $dir.'var/log';
        }

        $kernel = new Kernel($env, WP_DEBUG);
        $kernel->boot();
        $container = $kernel->getContainer();

        $loader     = $container->get('dotenv');
        $projectDir = $kernel->getProjectDir();

        // loads .env, .env.local, and .env.$APP_ENV.local or .env.$APP_ENV
        $loader->loadEnv($projectDir.'/.env');

        return $container;
    }

    static function getRepository(): PivotRepository
    {
        $container = self::init();
        /**
         * @var PivotRepository $pivotRepository
         */
        $pivotRepository = $container->get('pivotRepository');

        return $pivotRepository;
    }

    static function getTypeOffreRepository(): TypeOffreRepository
    {
        $container = self::init();
        /**
         * @var TypeOffreRepository $typeOffreRepository
         */
        $typeOffreRepository = $container->get('typeOffreRepository');

        return $typeOffreRepository;
    }

    static function getRemoteRepository(): PivotRemoteRepository
    {
        $container = self::init();
        /**
         * @var PivotRemoteRepository $pivotRemoteRepository
         */
        $pivotRemoteRepository = $container->get('pivotRemoteRepository');

        return $pivotRemoteRepository;
    }

}