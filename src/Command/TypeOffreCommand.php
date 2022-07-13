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
                $this->treatmentClassification($childObject);
            }
        }
    }

    private function treatmentClassification(TypeOffre $data)
    {
        $families = json_decode(
            $this->pivotRemoteRepository->thesaurusCategories($data->idType)
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
            $familyOffreType = $this->treatmentX($family, $data, 2);
            if (isset($family->spec)) {
                foreach ($family->spec as $family2) {
                    //limite branche classification
                    if ($family2->urn == UrnList::CLASSIFICATION->value) {
                        $family2OffreType = $this->treatmentX($family2, $familyOffreType, 4);
                        foreach ($family2->spec as $family3) {
                            $family3OffreType = $this->treatmentX($family3, $family2OffreType, 6);
                            if (isset($family3->spec)) {
                                foreach ($family3->spec as $family4) {
                                    $family4OffreType = $this->treatmentX($family4, $family3OffreType, 8);
                                    if (isset($family4->spec)) {
                                        foreach ($family4->spec as $family5) {
                                            $this->treatmentX($family5, $family4OffreType, 10);
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

    private function treatmentX(Family $family, ?TypeOffre $typeOffre, int $niv): ?TypeOffre
    {
        $object = $this->createTypeOffre($family, $typeOffre);
        $this->io->write(str_repeat('-', $niv).' '.$family->labelByLanguage('fr'));
        $this->io->writeln(' '.$family->urn);
        $this->typeOffreRepository->persist($typeOffre);

        return $object;
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

        if (!isset($data->value)) {
            $data->value = 'null';
        }

        if (!is_int($id)) {
            $id = 0;
        }

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
