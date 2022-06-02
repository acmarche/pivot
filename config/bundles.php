<?php

$bundles = [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    AcMarche\Pivot\AcMarchePivotBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class     => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class  => ['all' => true],
    Liip\ImagineBundle\LiipImagineBundle::class => ['all' => true],
];

if (class_exists(AcMarche\Bottin\AcMarcheBottinBundle::class)) {
    $bundles[AcMarche\Bottin\AcMarcheBottinBundle::class] =  ['all' => true];
}

return $bundles;
