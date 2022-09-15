<?php

namespace AcMarche\Pivot\DependencyInjection;

use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;

class PivotContainer
{
    use ContainerAwareTrait;

    public function __construct()
    {
        $this->setContainer(self::init());
    }

    static function init(): ContainerInterface
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            Debug::enable();
            $env = 'dev';
        } else {
            if (!defined('WP_DEBUG')) {
                define('WP_DEBUG', false);
            }
            $env = 'prod';
        }

        $kernel = new Kernel($env, WP_DEBUG);
        (new Dotenv())
            ->bootEnv($kernel->getProjectDir().'/.env');

        $kernel->boot();

        return $kernel->getContainer();
    }

    static function getPivotRepository(): PivotRepository
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

    function getService(string $service): ?object
    {
        if ($this->container->has($service)) {
            return $this->container->get($service);
        }

        return null;
    }

}