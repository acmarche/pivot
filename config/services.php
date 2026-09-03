<?php

use AcMarche\PivotAi\Api\PivotClient;
use AcMarche\PivotAi\Api\ThesaurusClient;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {

    $containerConfigurator->parameters()
        // Where the SQLite offer index lives. Override with PIVOT_DB_PATH to
        // move it off the data directory (faster disk, tmpfs, ...).
        ->set('pivot.db_path.default', '%kernel.project_dir%/data/pivot/offers.sqlite')
        ->set('pivot.db_path', '%env(default:pivot.db_path.default:PIVOT_DB_PATH)%');

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
