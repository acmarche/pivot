<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Entity\Filtre;
use AcMarche\Pivot\Parser\ParserEventTrait;
use AcMarche\Pivot\Repository\FiltreRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Spec\SpecTrait;
use AcMarche\Pivot\Spec\UrnUtils;
use AcMarche\Pivot\Utils\GenerateClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'pivot:filtre',
    description: 'Extrait tous les filtres des offres par type',
)]
class PivotFiltreCommand extends Command
{
    use SpecTrait, ParserEventTrait;

    private SymfonyStyle $io;
    private OutputInterface $output;

    public function __construct(
        private GenerateClass $generateClass,
        private PivotRepository $pivotRepository,
        private FiltreRepository $filtreRepository,
        private UrnUtils $urnUtils,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->output = $output;

        $this->createListing();

        return Command::SUCCESS;
    }

    private function createListing()
    {
        foreach ($this->pivotRepository->getTypesRootForCreateFiltres() as $root) {
            $this->filtreRepository->persist($root);
            $this->io->section($root->nom);
            $types = $this->pivotRepository->getSousTypesForCreateFiltres($root);
            foreach ($types as $filtre) {
                $this->treatmentChild($filtre);
            }
        }
        $this->filtreRepository->flush();
    }

    private function treatmentChild(Filtre $filtre): Filtre
    {
        $this->io->title($filtre->nom);
        if (!$this->filtreRepository->findByUrn($filtre->urn)) {
            $this->filtreRepository->persist($filtre);
        }

        return $filtre;
    }
}
