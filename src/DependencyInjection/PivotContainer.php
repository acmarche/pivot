<?php

namespace AcMarche\Pivot\DependencyInjection;

use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use AcMarche\Pivot\Repository\UrnDefinitionRepository;
use AcMarche\Pivot\Utils\LocalSwitcherPivot;
use AcMarche\PivotSearch\Search\SearchMeili;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;

class PivotContainer
{
    private ContainerInterface $container;

    public function __construct(bool $debug = false)
    {
        $this->init($debug);
    }

    private function init(bool $debug = false): void
    {
        if ($debug) {
            Debug::enable();
            $env = 'dev';
        } else {
            $env = 'prod';
        }

        //todo try
        $containerBuilder = new ContainerBuilder();

        $kernel = new Kernel($env, $debug);
        (new Dotenv())
            ->bootEnv($kernel->getProjectDir().'/.env');

        $kernel->boot();

        $this->container = $kernel->getContainer();
    }

    public static function getPivotRepository(bool $debug = false): PivotRepository
    {
        $container = new self($debug);

        /**
         * @var PivotRepository
         */
        return $container->getService('pivotRepository');
    }

    public static function getTypeOffreRepository(bool $debug = false): TypeOffreRepository
    {
        $container = new self($debug);

        /**
         * @var TypeOffreRepository
         */
        return $container->getService('typeOffreRepository');
    }

    public static function getUrnDefinitionRepository(bool $debug = false): UrnDefinitionRepository
    {
        $container = new self($debug);

        /**
         * @var UrnDefinitionRepository
         */
        return $container->getService('urnDefinitionRepository');
    }

    public static function getRemoteRepository(bool $debug = false): PivotRemoteRepository
    {
        $container = new self($debug);

        /**
         * @var PivotRemoteRepository
         */
        return $container->getService('pivotRemoteRepository');
    }

    public static function getLocalSwitcherPivot(bool $debug = false): LocalSwitcherPivot
    {
        $container = new self($debug);

        /**
         * @var LocalSwitcherPivot
         */
        return $container->getService('localSwitcherPivot');
    }

    public function getService(string $service): ?object
    {
        if ($this->container->has($service)) {
            return $this->container->get($service);
        }

        return null;
    }

    public static function getSearchMeili(bool $debug = false): SearchMeili
    {
        $container = new self($debug);

        /**
         * @var SearchMeili
         */
        return $container->getService('searchMeili');
    }
}
