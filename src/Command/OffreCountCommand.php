<?php

namespace AcMarche\Pivot\Command;

use Exception;
use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entity\TypeOffre;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use AcMarche\Pivot\Serializer\PivotSerializer;
use AcMarche\Pivot\TypeOffre\FilterUtils;
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
    private array $offres = [];

    public function __construct(
        private readonly PivotRepository $pivotRepository,
        private readonly TypeOffreRepository $typeOffreRepository,
        private readonly PivotRemoteRepository $pivotRemoteRepository,
        private readonly PivotSerializer $pivotSerializer,
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
        $this->io->writeln($count . ' ');
        $typeOffre->countOffres = $count;
    }

    private function draft()
    {
        $responseQuery = $this->pivotRepository->getAllDataFromRemote();
        foreach ($responseQuery->offre as $offreShort) {
            try {
                $dataString = $this->pivotRemoteRepository->offreByCgt($offreShort->codeCgt);
                $tmp = json_decode($dataString, null, 512, JSON_THROW_ON_ERROR);
                $dataStringOffre = json_encode($tmp->offre[0], JSON_THROW_ON_ERROR);

                $object = $this->pivotSerializer->deserializeToClass($dataStringOffre, Offre::class);
                if ($object) {
                    $object->dataRaw = $dataString;
                }
                $this->offres[] = $object;
            } catch (Exception $exception) {
                dump($exception);

                return Command::FAILURE;
            }
        }

        // $offres = FilterUtils::filterByTypeIdsOrUrns($this->offres, [], [$typeOffre->urn]);
        return Command::FAILURE;
    }
}
