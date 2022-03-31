<?php

use AcMarche\Pivot\Utils\SerializerPivot;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services->load('AcMarche\Pivot\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{Entities,Tests}']);

    $services->load('Symfony\Component\Serializer\\', __DIR__.'/../src/*');

    $services->set(SerializerPivot::class)
        ->arg('$serializer', service(SerializerInterface::class))
        ->public();

    $containerConfigurator->
    $services->set(SerializerInterface::class)
        ->autowire()
        ->autoconfigure()
        ->public();

};
