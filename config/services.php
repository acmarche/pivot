<?php

use AcMarche\PivotAi\Api\PivotClient;
use AcMarche\PivotAi\Api\ThesaurusClient;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {

    $services = $containerConfigurator->services();
    $services = $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('AcMarche\PivotAi\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{Entity,Tests}']);

    $services->set(PivotClient::class)->public();
    $services->set(ThesaurusClient::class)->public();

};
