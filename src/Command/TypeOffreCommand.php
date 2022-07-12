<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Entity\TypeOffre;
use AcMarche\Pivot\Parser\ParserEventTrait;
use AcMarche\Pivot\Repository\TypeOffreRepository;
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
    name: 'pivot:types-offre',
    description: 'Extrait tous les types d\'offres',
)]
class TypeOffreCommand extends Command
{
    use SpecTrait, ParserEventTrait;

    private SymfonyStyle $io;
    private OutputInterface $output;

    public function __construct(
        private GenerateClass $generateClass,
        private PivotRepository $pivotRepository,
        private TypeOffreRepository $typeOffreRepository,
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
        foreach ($this->pivotRepository->getTypesRootForCreateTypesOffre() as $root) {
            $this->typeOffreRepository->persist($root);
            $this->io->section($root->nom);
            try {
                $types = $this->pivotRepository->getSousTypesForCreateTypesOffre($root);
            } catch (\Exception $e) {
                $this->io->error($e->getMessage());
                continue;
            }
            foreach ($types as $type) {
                $this->io->writeln($type->nom);
                $this->treatmentChild($type);
            }
        }
        $this->typeOffreRepository->flush();
    }

    private function treatmentChild(TypeOffre $typeOffre): TypeOffre
    {
        if (!$this->typeOffreRepository->findByUrn($typeOffre->urn)) {
            $this->typeOffreRepository->persist($typeOffre);
        }

        return $typeOffre;
    }
}
