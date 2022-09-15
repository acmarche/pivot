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

    private static function init(): ContainerInterface
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
        $container = new self();

        /**
         * @var PivotRepository
         */
        return $container->getService('pivotRepository');
    }

    static function getTypeOffreRepository(): TypeOffreRepository
    {
        $container = new self();

        /**
         * @var TypeOffreRepository
         */
        return $container->getService('typeOffreRepository');
    }

    static function getRemoteRepository(): PivotRemoteRepository
    {
        $container = new self();

        /**
         * @var PivotRemoteRepository
         */
        return $container->getService('pivotRemoteRepository');

    }

    function getService(string $service): ?object
    {
        if ($this->container->has($service)) {
            return $this->container->get($service);
        }

        return null;
    }

}