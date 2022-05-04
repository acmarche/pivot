<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Entity\Filtre;
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
        $this->io = new SymfonyStyle($input, $output);
        $this->output = $output;

        $this->createListing();

        return Command::SUCCESS;
    }

    private function createListing()
    {
        $types = $this->pivotRepository->getTypesOffre();

        foreach ($types as $id => $nom) {
            $parent = $this->treatment($id, $nom, null);
            $this->io->section($nom);
            $offres = $this->pivotRepository->getOffres([$id]);
            $count = count($offres);
            $this->io->title("$count offres trouvées");
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
            foreach ($rows as $childTab) {
                $this->treatment($childTab[0], $childTab[1], $parent);
            }
            $table = new Table($this->output);
            $table
                ->setHeaders(['Id', 'Nom'])
                ->setRows($rows);
            $table->render();
        }
        $this->filtreRepository->flush();
    }

    private function treatment(int $id, string $nom, ?Filtre $parent): Filtre
    {
        if (!$filtre = $this->filtreRepository->findByReference($id)) {
            $filtre = new Filtre($id, $nom, $parent);
            $this->filtreRepository->persist($filtre);
        }

        return $filtre;
    }
}
