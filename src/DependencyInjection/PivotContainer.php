<?php

namespace AcMarche\Pivot\DependencyInjection;

use AcMarche\Pivot\Repository\FiltreRepository;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ErrorHandler\Debug;

class PivotContainer
{
    public function __construct()
    {

    }

    static function init(): ContainerInterface
    {
        if (WP_DEBUG) {
            Debug::enable();
        }
        $env = WP_DEBUG ? 'dev' : 'prod';
        $kernel = new Kernel($env, WP_DEBUG);
        $kernel->boot();
        $container = $kernel->getContainer();

        $loader = $container->get('dotenv');
        // AcMarche/Pivot
        $projectDir = $kernel->getProjectDir();
        $loader->loadEnv($projectDir.'/../../.env');

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

    static function getFiltreRepository(): FiltreRepository
    {
        $container = self::init();
        /**
         * @var FiltreRepository $filtreRepository
         */
        $filtreRepository = $container->get('filtreRepository');

        return $filtreRepository;
    }

    static function getRemoteRepository(): PivotRemoteRepository
    {
        $container = self::init();
        /**
         * @var PivotRemoteRepository $pivotRepository
         */
        $pivotRepository = $container->get('pivotRemoteRepository');

        return $pivotRepository;
    }

}