<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Entity\TypeOffre;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'pivot:offre-count',
    description: 'Compte les offres pour chaque types d\'offre. --flush pour enregister dans la DB',
)]
class OffreCountCommand extends Command
{
    private SymfonyStyle $io;
    private OutputInterface $output;

    public function __construct(
        private readonly PivotRepository $pivotRepository,
        private readonly TypeOffreRepository $typeOffreRepository,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addOption('flush', "flush", InputOption::VALUE_NONE, 'Enregistrer dans la DB');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->output = $output;

        $flush = (bool)$input->getOption('flush');

        $typesOffre = $this->typeOffreRepository->findAll();

        foreach ($typesOffre as $typeOffre) {
            $this->io->section($typeOffre->name);
            $this->io->writeln($typeOffre->urn);
            $this->setCount($typeOffre);
        }

        if ($flush) {
            $this->typeOffreRepository->flush();
        }

        return Command::SUCCESS;
    }

    private function setCount(TypeOffre $typeOffre)
    {
        $offres = $this->pivotRepository->fetchOffres([$typeOffre], false);
        $count = count($offres);
        $this->io->writeln($count.' ');
        $typeOffre->countOffres = $count;
    }
}
