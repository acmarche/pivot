<?php

use AcMarche\Pivot\Jf;
use AcMarche\Pivot\Parser\PivotParser;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Spec\UrnUtils;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;

return static function (ContainerConfigurator $containerConfigurator): void {

    $containerConfigurator->parameters()
        ->set('mailer.transport', '%env(HADES_URL)%')
        ->set('kernel.cache_dir', 'var/cache');

    $services = $containerConfigurator->services()
        ->defaults()
        #Automatically injects dependencies in your services
        ->autowire()
        #Automatically registers your services as commands, event subscribers, etc.
        ->autoconfigure()
        # Allows optimizing the container by removing unused services; this also means
        # fetching services directly from the container via $container->get() won't work
        ->private();

    #Makes classes in src/ available to be used as services;
    #this creates a service per class whose id is the fully-qualified class name
    $services->load('AcMarche\Pivot\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{Entities,Tests}']);


    $services->set('pivotRepository', PivotRepository::class)
        ->public();

    $services->set('pivotRemoteRepository', PivotRemoteRepository::class);
    $services->set('urnUtils', UrnUtils::class);
    $services->set('pivotParser', PivotParser::class);

    $services->set('Jfs', Jf::class)
        ->public();

    $services->set('slugger', AsciiSlugger::class);
    $services->set('dotenv', Dotenv::class)->public();

    $services->alias(PivotParser::class, 'pivotParser');
    $services->alias(UrnUtils::class, 'urnUtils');
    $services->alias(SluggerInterface::class, 'slugger');


};
