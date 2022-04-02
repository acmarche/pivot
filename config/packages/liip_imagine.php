<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('liip_imagine', [
        'resolvers' => [
            'default' => [
                'web_path' => null,
            ],
        ],
    ]);

    $containerConfigurator->extension(
        'liip_imagine',
        [
            'filter_sets' => [
                'cache' => null,
                'organigramme_thumb' => [
                    'quality' => 100,
                    'filters' => [
                        'thumbnail' => [
                            'size' => [150, 150],
                            'mode' => 'outbound',
                            
                        ],
                    ],
                    
                ],
            ],
        ]
    );
};
