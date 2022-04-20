<?php

namespace AcMarche\Pivot\DependencyInjection;

use AcMarche\Pivot\Repository\PivotRepository;
use Symfony\Component\ErrorHandler\Debug;

class PivotContainer
{
    public function __construct()
    {

    }

    static function getRepository(): PivotRepository
    {
        if (WP_DEBUG) {
            Debug::enable();
        }
        $env    = WP_DEBUG ? 'dev' : 'prod';
        $kernel = new Kernel($env, WP_DEBUG);
        $kernel->boot();
        $container = $kernel->getContainer();

        $loader = $container->get('dotenv');
        $loader->loadEnv('.env');
        /**
         * @var PivotRepository $pivotRepository
         */
        $pivotRepository = $container->get('pivotRepository');

        return $pivotRepository;
    }

}