<?php

namespace AcMarche\Pivot\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

//https://symfony.com/doc/7.0/configuration/micro_kernel_trait.html
class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
