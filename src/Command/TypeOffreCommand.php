<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Entities\Family\Family;
use AcMarche\Pivot\Entity\TypeOffre;
use AcMarche\Pivot\Parser\ParserEventTrait;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use AcMarche\Pivot\Serializer\PivotSerializer;
use AcMarche\Pivot\Spec\SpecSearchTrait;
use AcMarche\Pivot\Spec\UrnList;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'pivot:generate-types-offre',
    description: 'Génère une table sql avec tous les types d\'offres. --flush pour enregistrer dans la DB',
)]
class TypeOffreCommand extends Command
{
    use SpecSearchTrait, ParserEventTrait;

    private SymfonyStyle $io;
    private OutputInterface $output;

    public function __construct(
        private readonly PivotRepository $pivotRepository,
        private readonly PivotRemoteRepository $pivotRemoteRepository,
        private readonly TypeOffreRepository $typeOffreRepository,
        private readonly PivotSerializer $pivotSerializer
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('flush', "flush", InputOption::VALUE_NONE, 'Enregistrer les données dans la DB');
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
                $this->typeOffreRepository->persist($childObject);
                $this->treatmentClassification($childObject);
            }
        }
    }

    private function treatmentClassification(TypeOffre $data)
    {
        $familiesStd = json_decode(
            $this->pivotRemoteRepository->thesaurusCategories($data->typeId),
            null,
            512,
            JSON_THROW_ON_ERROR
        );
        $this->io->writeln($this->pivotRemoteRepository->url_executed);
        /**
         * @var Family[] $families
         */
        $families = $this->pivotSerializer->deserializeToClass(
            json_encode($familiesStd->spec, JSON_THROW_ON_ERROR),
            'AcMarche\Pivot\Entities\Family\Family[]',
        );
        foreach ($families as $family) {
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
                                            $family5OffreType = $this->treatmentX($family5, $family4OffreType, 10);
                                            if (isset($family5->spec)) {
                                                foreach ($family5->spec as $family6) {
                                                    $this->treatmentX($family6, $family5OffreType, 12);
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
        }
    }

    private function treatmentX(Family $family, ?TypeOffre $typeOffre, int $niv): ?TypeOffre
    {
        $object = $this->createTypeOffre($family, $typeOffre);
        $this->io->write(str_repeat('-', $niv) . ' ' . $family->labelByLanguage('fr'));
        $this->io->writeln(' ' . $family->urn);
        $this->typeOffreRepository->persist($object);

        return $object;
    }

    private function createTypeOffre(Family $data, ?TypeOffre $root): TypeOffre
    {
        $tab = explode(':', $data->urn);

        $id = (int)end($tab);

        return new TypeOffre(
            $data->labelByLanguage('fr'),
            $id,
            $data->order,
            $data->value,//code
            $data->urn,
            $data->type,
            $root
        );
    }
}
