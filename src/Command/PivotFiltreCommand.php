<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Entities\Filtre;
use AcMarche\Pivot\Parser\ParserEventTrait;
use AcMarche\Pivot\Repository\FiltreRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Spec\SpecTrait;
use AcMarche\Pivot\Spec\UrnSubCatList;
use AcMarche\Pivot\Spec\UrnUtils;
use AcMarche\Pivot\Utils\GenerateClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
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
        $io = new SymfonyStyle($input, $output);
        $types = $this->pivotRepository->getTypesOffre();
        $csv = [];

        foreach ($types as $id => $type) {
            $filtre = new Filtre($id, $type, 0);
            $this->filtreRepository->persist($filtre);
            $io->section($type);
            $offres = $this->pivotRepository->getOffres([$id]);
            $count = count($offres);
            $io->title("$count offres trouvées");
            $rows = [];
            break;
            foreach ($offres as $offre) {
                $this->specs = $offre->spec;
                $classements = $this->findByUrnSubCat(UrnSubCatList::CLASSIF);
                foreach ($classements as $classement) {
                    $info = $this->urnUtils->getInfosUrn($classement->urn);
                    if ($classement->type == 'Boolean') {
                        $filtre = new Filtre($classement, $info->labelByLanguage('fr'), $id);
                        $this->filtreRepository->persist($filtre);
                        $rows[$classement->order] = [$classement->order, $info->labelByLanguage('fr')];
                        // $io->writeln($info->labelByLanguage('fr'));
                    }
                }
            }
            $this->filtreRepository->flush();
            $table = new Table($output);
            $table
                ->setHeaders(['Id', 'Nom'])
                ->setRows($rows);
            $table->render();
        }

        return Command::SUCCESS;
    }
}
