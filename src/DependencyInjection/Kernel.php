<?php

namespace AcMarche\Pivot\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

//https://symfony.com/doc/6.1/configuration/micro_kernel_trait.html
class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getCacheDir(): string
    {
        return dirname(__DIR__).'/../../../var/cache';
    }

    public function getLogDir(): string
    {
        return dirname(__DIR__).'/../../../var/log';
    }

    public static function getDir() {
        return dirname(__DIR__).'/../../../';
    }

}
