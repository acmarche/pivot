<?php

use AcMarche\Pivot\Parser\PivotParser;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Spec\UrnUtils;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

return static function (ContainerConfigurator $containerConfigurator): void {

    $containerConfigurator->parameters()
        ->set('mailer.transport', '%env(HADES_URL)%');

    $services = $containerConfigurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    $services->load('AcMarche\Pivot\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{Entities,Tests}']);

    $services->set('cache', FilesystemAdapter::class)
        ->args([
            '$namespace' => 'zeze',
            '$defaultLifetime' => 20000,
            '$directory' => 'var/cache',
        ]);

    $services->set('pivotRepository', PivotRepository::class);
    $services->set('pivotRemoteRepository', PivotRemoteRepository::class);
    $services->set('urnUtils', UrnUtils::class);
    $services->set('pivotParser', PivotParser::class);
    $services->set('serializer', Serializer::class);
    $services->set('slugger', AsciiSlugger::class);
    $services->set('dotenv', Dotenv::class);

    $services->alias(CacheInterface::class, 'cache');
    $services->alias(SerializerInterface::class, 'serializer');
    $services->alias(PivotParser::class, 'pivotParser');
    $services->alias(UrnUtils::class, 'urnUtils');
    $services->alias(SluggerInterface::class, 'slugger');

    //   $services->load('Symfony\Component\Serializer\\', __DIR__.'/../src/*');

    /*  $services->set(SerializerPivot::class)
          ->arg('$serializer', service(SerializerInterface::class))
          ->public();

      $containerConfigurator->
      $services->set(SerializerInterface::class)
          ->autowire()
          ->autoconfigure()
          ->public();*/
};
