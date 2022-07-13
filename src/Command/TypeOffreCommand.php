<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Entities\Family\Family;
use AcMarche\Pivot\Entity\TypeOffre;
use AcMarche\Pivot\Parser\ParserEventTrait;
use AcMarche\Pivot\Parser\PivotSerializer;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use AcMarche\Pivot\Spec\SpecTrait;
use AcMarche\Pivot\Spec\UrnList;
use AcMarche\Pivot\Spec\UrnTypeList;
use AcMarche\Pivot\Spec\UrnUtils;
use AcMarche\Pivot\Utils\GenerateClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'pivot:types-offre',
    description: 'Génère une table avec tous les types d\'offres',
)]
class TypeOffreCommand extends Command
{
    use SpecTrait, ParserEventTrait;

    private SymfonyStyle $io;
    private OutputInterface $output;

    public function __construct(
        private GenerateClass $generateClass,
        private PivotRepository $pivotRepository,
        private PivotRemoteRepository $pivotRemoteRepository,
        private TypeOffreRepository $typeOffreRepository,
        private UrnUtils $urnUtils,
        private PivotSerializer $pivotSerializer
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
        $this->output = $output;

        $this->createListing();
        $flush = (bool)$input->getOption('flush');
        if ($flush) {
            $this->typeOffreRepository->flush();
        }

        return Command::SUCCESS;
    }

    private function createListing()
    {
        $families = $this->pivotRepository->thesaurusFamilies();

        foreach ($families as $family) {
            $this->io->section($family->labelByLanguage('fr'));
            $root = $this->createTypeOffre($family, null);
            $this->typeOffreRepository->persist($root);
            foreach ($family->spec as $child) {
                $this->io->writeln($child->labelByLanguage('fr'));
                $childObject = $this->createTypeOffre($child, $root);
                $this->treatmentChild($childObject);
                $this->treatmentClassification($childObject->idType);
            }
        }
    }

    private function treatmentClassification(int $typeId)
    {
        $families = json_decode(
            $this->pivotRemoteRepository->thesaurusCategories($typeId)
        );
        //$this->io->writeln($this->pivotRemoteRepository->url_executed);
        /**
         * @var Family[] $t
         */
        $t = $this->pivotSerializer->deserializeToClass(
            json_encode($families->spec),
            'AcMarche\Pivot\Entities\Family\Family[]',
        );
        foreach ($t as $family) {
            $this->io->write('-- '.$family->labelByLanguage('fr'));
            $this->io->writeln(' '.$family->urn);
            if (isset($family->spec)) {
                foreach ($family->spec as $family2) {
                    //limite branche classification
                    if ($family2->urn == UrnList::CLASSIFICATION->value) {
                        $this->io->write('---- '.$family2->labelByLanguage('fr'));
                        $this->io->writeln(' '.$family2->urn);
                        foreach ($family2->spec as $family3) {
                            $this->io->write('------ '.$family3->labelByLanguage('fr'));
                            $this->io->writeln(' '.$family3->urn);
                            if (isset($family3->spec)) {
                                foreach ($family3->spec as $family4) {
                                    $this->io->write('-------- '.$family4->labelByLanguage('fr'));
                                    $this->io->writeln(' '.$family4->urn);
                                    if (isset($family4->spec)) {
                                        foreach ($family4->spec as $family5) {
                                            $this->io->write('---------- '.$family5->labelByLanguage('fr'));
                                            $this->io->writeln(' '.$family5->urn);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function treatmentX(Family $family) {
        $this->io->write('---- '.$family->labelByLanguage('fr'));
        $this->io->writeln(' '.$family->urn);
    }

    private function treatmentChild(TypeOffre $typeOffre): TypeOffre
    {
        if (!$this->typeOffreRepository->findByUrn($typeOffre->urn)) {
            $this->typeOffreRepository->persist($typeOffre);
        }

        return $typeOffre;
    }

    private function createTypeOffre(Family $data, ?TypeOffre $root): TypeOffre
    {
        list($a, $b, $id) = explode(':', $data->urn);

        return new TypeOffre(
            $data->labelByLanguage('fr'),
            $id,
            $data->order,
            $data->value,
            $data->urn,
            $data->type,
            $root
        );
    }
}
