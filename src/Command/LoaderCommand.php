<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Entities\Pivot\Event;
use AcMarche\Pivot\Entities\Pivot\SpecEvent;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Serializer\DependencyInjection\SerializerPass;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

#[AsCommand(
    name: 'pivot:test',
    description: 'Add a short description for your command',
)]
class LoaderCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private SerializerInterface $serializer,
        private PivotRemoteRepository $pivotRemoteRepository,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Charge le xml de hades');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //$this->generateClass->generateTypeUrn();
        // echo($this->pivotRemoteRepository->getThesaurus(Thesaurus::THESAURUS_TYPE_OFFRE));
        $this->io = new SymfonyStyle($input, $output);

        $this->detailOffre("EVT-A1-0016-2D6U");

        return Command::SUCCESS;
    }

    private function detailOffre(string $codeCgt)
    {
        $dataString = $this->pivotRemoteRepository->offreByCgt($codeCgt);
        $tmp = json_decode($dataString);
        $dataString = json_encode($tmp->offre[0]);
        $event = $this->serializer->deserialize($dataString, Event::class, 'json');

        $eventSpec = new SpecEvent($event->spec);
        dump($eventSpec->getDates());

    }

    private function initDi()
    {
        $containerBuilder = new ContainerBuilder();
        $loader = new PhpFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__.'/../../config')
        );

        //    $loader->load('services.php');
        dump($containerBuilder->getServiceIds());

        $containerBuilder->addCompilerPass(new SerializerPass());
        //$containerBuilder->set(SerializerInterface::class, service(Serializer::class));
        // $containerBuilder->register(SerializerInterface::class, Serializer::class);
        $containerBuilder->compile();
        dump($containerBuilder->getServiceIds());

        $serializer = $containerBuilder->get(Serializer::class);
    }

}
