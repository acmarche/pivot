<?php

namespace AcMarche\Pivot;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class AcMarchePivotBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/packages/twig.php');
        $container->import('../config/packages/framework.php');
        $container->import('../config/packages/cache.php');
        if ($builder->hasExtension('monolog')) {
            $container->import('../config/packages/monolog.php');
        }
        $container->import('../config/packages/doctrine.php');
    }
}
