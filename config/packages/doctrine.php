<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'doctrine',
        [
            'dbal'=>[
                'url'=>'%env(resolve:DATABASE_URL)%'
            ],
            'orm' => [
                'mappings' => [
                    'AcMarche\Pivot' => [
                        'is_bundle' => false,
                        'dir' => '%kernel.project_dir%/src/Entity',
                        'prefix' => 'AcMarche\Pivot',
                        'alias' => 'AcMarche\Pivot',
                    ],
                ],
            ],
        ]
    );
};