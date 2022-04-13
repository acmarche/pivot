<?php

use AcMarche\Pivot\Jf;
use AcMarche\Pivot\Parser\PivotParser;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Spec\UrnUtils;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {

    $containerConfigurator->parameters()
        ->set('mailer.transport', '%env(HADES_URL)%')
        ->set('kernel.cache_dir', 'var/cache');

    $services = $containerConfigurator->services()
        ->defaults()
        #Automatically injects dependencies in your services
        ->autowire()
        #Automatically registers your services as commands, event subscribers, etc.
        ->autoconfigure()
        # Allows optimizing the container by removing unused services; this also means
        # fetching services directly from the container via $container->get() won't work
        ->public();

    #Makes classes in src/ available to be used as services;
    #this creates a service per class whose id is the fully-qualified class name
    $services->load('AcMarche\Pivot\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{Entities,Tests}']);

    $services->set('cache', FilesystemAdapter::class)
        ->args([
            '$namespace' => 'zeze',
            '$defaultLifetime' => 20000,
            '$directory' => 'var/cache',
        ]);

    $services->set('pivotRepository', PivotRepository::class);
    $services->set('pivotRemoteRepository', PivotRemoteRepository::class);
    $services->set('urnUtils', UrnUtils::class);
    $services->set('pivotParser', PivotParser::class);

    $services->set('Jfs', Jf::class)
        ->public();

    $services->set('serializer', Serializer::class)
        ->args([tagged_iterator('serializer.normalizer'), tagged_iterator('serializer.encoder')]);

    $services->set('slugger', AsciiSlugger::class);
    $services->set('dotenv', Dotenv::class);

    $services->alias(CacheInterface::class, 'cache');
    $services->alias(SerializerInterface::class, 'serializer');

    $services->alias(PivotParser::class, 'pivotParser');
    $services->alias(UrnUtils::class, 'urnUtils');
    $services->alias(SluggerInterface::class, 'slugger');

    $services->instanceof(Command::class)
        ->tag('console.command');

    /**
     * @see FrameworkExtension
     * @see SerializerPass
     *
     * $services
     * ->instanceof(EncoderInterface::class)
     * // services whose classes are instances of CustomInterface will be tagged automatically
     * ->tag('serializer.encoder');
     *
     * $services
     * ->instanceof(DecoderInterface::class)
     * // services whose classes are instances of CustomInterface will be tagged automatically
     * ->tag('serializer.encoder');
     * $services
     * ->instanceof(NormalizerInterface::class)
     * // services whose classes are instances of CustomInterface will be tagged automatically
     * ->tag('serializer.normalizer');
     * $services
     * ->instanceof(DenormalizerInterface::class)
     * // services whose classes are instances of CustomInterface will be tagged automatically
     * ->tag('serializer.normalizer');*/


    //   $services->load('Symfony\Component\Serializer\\', __DIR__.'/../src/*');

    /*  $services->set(SerializerPivot::class)
          ->arg('$serializer', service(SerializerInterface::class))
          ->public();

      $containerConfigurator->
      $services->set(SerializerInterface::class)
          ->autowire()
          ->autoconfigure()
          ->public();*/
};
