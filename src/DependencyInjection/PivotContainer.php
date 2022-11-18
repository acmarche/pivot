<?php

namespace AcMarche\Pivot\DependencyInjection;

use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use AcMarche\Pivot\Repository\UrnDefinitionRepository;
use AcMarche\Pivot\Utils\LocalHelper;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;

class PivotContainer
{
    use ContainerAwareTrait;

    public function __construct(bool $debug = false)
    {
        $this->setContainer(self::init($debug));
    }

    private static function init(bool $debug = false): ContainerInterface
    {
        if ($debug) {
            Debug::enable();
            $env = 'dev';
        } else {
            $env = 'prod';
        }

        $kernel = new Kernel($env, $debug);
        (new Dotenv())
            ->bootEnv($kernel->getProjectDir().'/.env');

        $kernel->boot();

        return $kernel->getContainer();
    }

    static function getPivotRepository(bool $debug = false): PivotRepository
    {
        $container = new self($debug);

        /**
         * @var PivotRepository
         */
        return $container->getService('pivotRepository');
    }

    static function getTypeOffreRepository(bool $debug = false): TypeOffreRepository
    {
        $container = new self($debug);

        /**
         * @var TypeOffreRepository
         */
        return $container->getService('typeOffreRepository');
    }

    static function getUrnDefinitionRepository(bool $debug = false): UrnDefinitionRepository
    {
        $container = new self($debug);

        /**
         * @var UrnDefinitionRepository
         */
        return $container->getService('urnDefinitionRepository');
    }

    static function getRemoteRepository(bool $debug = false): PivotRemoteRepository
    {
        $container = new self($debug);

        /**
         * @var PivotRemoteRepository
         */
        return $container->getService('pivotRemoteRepository');

    }

    static function getLocalSwitcherPivot(bool $debug = false): LocalHelper
    {
        $container = new self($debug);

        /**
         * @var LocalHelper
         */
        return $container->getService('localHelperPivot');
    }

    function getService(string $service): ?object
    {
        if ($this->container->has($service)) {
            return $this->container->get($service);
        }

        return null;
    }

}