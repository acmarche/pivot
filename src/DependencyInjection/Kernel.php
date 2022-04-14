<?php

namespace AcMarche\Pivot\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getCacheDir(): string
    {

        return '/var/www/new/var/cache';
    }

    public function getLogDir(): string
    {
        return '/var/www/new/var/log';
    }


}
