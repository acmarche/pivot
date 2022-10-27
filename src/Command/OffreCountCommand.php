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
        private PivotRepository $pivotRepository,
        private TypeOffreRepository $typeOffreRepository,
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

        $this->askType();

        if ($flush) {
            $this->typeOffreRepository->flush();
        }

        return Command::SUCCESS;
    }

    protected function askType()
    {
        $this->pivotRepository->getOffres([]);
        $roots = $this->typeOffreRepository->findRoots();

        foreach ($roots as $root) {
            $this->io->section($root->nom);
            $this->setCount($root, 1);
            $lvl1 = $this->typeOffreRepository->findByParent($root->id);
            foreach ($lvl1 as $lvl2) {
                $this->setCount($lvl2, 2);
                $lvl2->children = $this->typeOffreRepository->findByParent($lvl2->id);
                foreach ($lvl2->children as $lvl3) {
                    $this->setCount($lvl3, 4);
                    $lvl3->children = $this->typeOffreRepository->findByParent($lvl3->id);
                    foreach ($this->typeOffreRepository->findByParent($lvl3->id) as $lvl4) {
                        $this->setCount($lvl4, 6);
                        $lvl4->children = $this->typeOffreRepository->findByParent($lvl4->id);
                        foreach ($this->typeOffreRepository->findByParent($lvl4->id) as $lvl5) {
                            $this->setCount($lvl5, 8);
                            $lvl5->children = $this->typeOffreRepository->findByParent($lvl5->id);
                            foreach ($this->typeOffreRepository->findByParent($lvl5->id) as $lvl6) {
                                $this->setCount($lvl6, 10);
                                $lvl6->children = $this->typeOffreRepository->findByParent($lvl6->id);
                                foreach ($this->typeOffreRepository->findByParent($lvl6->id) as $lvl7) {
                                    $this->setCount($lvl7, 10);
                                    $lvl7->children = $this->typeOffreRepository->findByParent($lvl7->id);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function setCount(TypeOffre $typeOffre, int $lvl)
    {
        $count = count($this->pivotRepository->getOffres([$typeOffre]));
        $this->io->write(str_repeat('-', $lvl).' '.$typeOffre->nom.' => ');
        $this->io->writeln($count.' ');
        $typeOffre->countOffres = $count;
    }

}
