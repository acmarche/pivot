<?php

namespace AcMarche\PivotAi;

use Symfony\Bundle\DebugBundle\DebugBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Twig\Extra\TwigExtraBundle\TwigExtraBundle;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        foreach ($this->getMyBundles() as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    private function getMyBundles(): array
    {
        $bundles = [
            FrameworkBundle::class => ['all' => true],
            TwigBundle::class => ['all' => true],
            TwigExtraBundle::class => ['all' => true],
            PivotAiBundle::class => ['all' => true],
            MonologBundle::class => ['all' => true],
        ];


        if (class_exists(DebugBundle::class)) {
            $bundles[DebugBundle::class] = ['dev' => true];
        }

        return $bundles;
    }
}
