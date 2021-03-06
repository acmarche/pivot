<?php


use Symfony\Config\DoctrineConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\Env;

return static function (DoctrineConfig $doctrine) {

    $doctrine->dbal()
        ->connection('pivot')
        ->url(env('DATABASE_PIVOT_URL')->resolve())
        ->charset('utf8mb4');

    $emMda = $doctrine->orm()->entityManager('pivot');
    $emMda->connection('pivot');
    $emMda->mapping('AcMarchePivot')
        ->isBundle(false)
        ->type('attribute')
        ->dir('%kernel.project_dir%/src/AcMarche/Pivot/src/Entity')
        ->prefix('AcMarche\Pivot')
        ->alias('AcMarchePivot');
};
