<?php

use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use AcMarche\Pivot\Repository\UrnDefinitionRepository;
use AcMarche\Pivot\Utils\LocalSwitcherPivot;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Dotenv\Dotenv;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator
        ->services()
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

    $services->set('dotenv', Dotenv::class)->public();

    $services->set('pivotRepository', PivotRepository::class)
        ->public();

    $services->set('pivotRemoteRepository', PivotRemoteRepository::class)
        ->public();

    $services->set('typeOffreRepository', TypeOffreRepository::class)
        ->public();

    $services->set('urnDefinitionRepository', UrnDefinitionRepository::class)
        ->public();

    $services->set('localSwitcherPivot', LocalSwitcherPivot::class)
        ->public();
};
