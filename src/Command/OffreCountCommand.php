<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Entity\TypeOffre;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'pivot:offre-count',
    description: 'Compte les offres pour chaque types d\'offre. --flush save in DB',
)]
class OffreCountCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly PivotRepository $pivotRepository,
        private readonly TypeOffreRepository $typeOffreRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('flush', "flush", InputOption::VALUE_NONE, 'Enregistrer dans la DB');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $flush = (bool)$input->getOption('flush');

        foreach ($this->typeOffreRepository->findAll() as $typeOffre) {
            $this->io->section($typeOffre->name);
            $this->io->writeln($typeOffre->urn);
            try {
                $this->setCount($typeOffre);
            } catch (InvalidArgumentException $e) {
                $this->io->error($e->getMessage());
            }
        }

        if ($flush) {
            $this->typeOffreRepository->flush();
        }

        return Command::SUCCESS;
    }

    /**
     * @param TypeOffre $typeOffre
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function setCount(TypeOffre $typeOffre): void
    {
        $offres = $this->pivotRepository->fetchOffres([$typeOffre], false);
        $count = count($offres);
        $this->io->writeln($count.' ');
        $typeOffre->countOffres = $count;
    }
}
