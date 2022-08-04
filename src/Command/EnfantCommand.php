<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Parser\PivotSerializer;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use AcMarche\Pivot\Spec\UrnList;
use AcMarche\Pivot\Spec\UrnTypeList;
use AcMarche\Pivot\Spec\UrnUtils;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'pivot:enfant',
    description: 'Enfants de x',
)]
class EnfantCommand extends Command
{
    private SymfonyStyle $io;
    private OutputInterface $output;

    public function __construct(
        private PivotRepository $pivotRepository,
        private PivotRemoteRepository $pivotRemoteRepository,
        private TypeOffreRepository $typeOffreRepository,
        private UrnUtils $urnUtils,
        private PivotSerializer $pivotSerializer
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
//        $families = $this->pivotRemoteRepository->thesaurus('typeofr/9/'.UrnList::CATEGORIE_EVENT->value);
        $families = $this->pivotRepository->thesaurusChildren(UrnTypeList::evenement()->typeId, UrnList::CATEGORIE_EVENT->value);
        $families = $this->pivotRepository->thesaurusChildren(UrnTypeList::hebergements()->typeId, UrnList::CATEGORIE_EVENT);
        foreach ($families as $family) {
            $io->write($family->label[0]->value);
            $io->writeln(' '.$family->urn);
        }

        return Command::SUCCESS;
    }

}
