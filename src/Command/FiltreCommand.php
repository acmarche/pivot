<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Parser\ParserEventTrait;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Spec\SpecTrait;
use AcMarche\Pivot\Spec\UrnSubCatList;
use AcMarche\Pivot\Spec\UrnUtils;
use AcMarche\Pivot\Utils\GenerateClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'pivot:filtre',
    description: 'Extrait tous les filtres des offres par type',
)]
class FiltreCommand extends Command
{
    use SpecTrait, ParserEventTrait;

    private SymfonyStyle $io;
    private OutputInterface $output;

    public function __construct(
        private GenerateClass $generateClass,
        private PivotRepository $pivotRepository,
        private UrnUtils $urnUtils,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addOption('categories', "ll", InputOption::VALUE_NONE, 'Liste les types');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        //$this->generateClass->generateTypeUrn();
        $categories = $input->getOption('categories');

        $types = $this->pivotRepository->getTypesOffre();

        foreach ($types as $id => $type) {
            $io->section($type);
            $offres = $this->pivotRepository->getOffres([$id]);
            $count = count($offres);
            $io->title("$count offres trouvées");
            $rows = [];
            foreach ($offres as $offre) {
                $this->specs = $offre->spec;
                $classements = $this->findByUrnSubCat(UrnSubCatList::CLASSIF);
                foreach ($classements as $classement) {
                    $info = $this->urnUtils->getInfosUrn($classement->urn);
                    if ($classement->type == 'Boolean') {
                        $rows[$classement->order] = [$classement->order, $info->labelByLanguage('fr')];
                       // $io->writeln($info->labelByLanguage('fr'));
                    }
                }
            }
            $table = new Table($output);
            $table
                ->setHeaders(['Id', 'Nom'])
                ->setRows($rows);
            $table->render();
        }

        return Command::SUCCESS;
    }


}
