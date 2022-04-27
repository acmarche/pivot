<?php

namespace AcMarche\Pivot\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

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



}
