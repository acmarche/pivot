<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Entities\Response\UrnResponse;
use AcMarche\Pivot\Entities\Urn\UrnDefinition;
use AcMarche\Pivot\Entity\UrnDefinitionEntity;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\UrnDefinitionRepository;
use AcMarche\Pivot\Serializer\PivotSerializer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/urn
 */
#[AsCommand(
    name: 'pivot:generate-urn',
    description: 'Génère une table sql avec tous les types d\'urns. --flush pour enregistrer dans la DB',
)]
class UrnCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly PivotRemoteRepository $pivotRemoteRepository,
        private readonly UrnDefinitionRepository $urnDefinitionRepository,
        private readonly PivotSerializer $pivotSerializer
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('flush', "flush", InputOption::VALUE_NONE, 'Enregistrer les données dans la DB');
        $this->addOption('urn', "urn", InputOption::VALUE_OPTIONAL, 'Urn');
        $this->addOption('lang', "lang", InputOption::VALUE_OPTIONAL, 'Urn', 'fr');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $urn = $input->getOption('urn');
        $lang = $input->getOption('lang');

        if ($urn) {
            $urnDefinition = $this->urnDefinitionRepository->findOneByUrn($urn);
            $this->io->writeln($urnDefinition->labelByLanguage($lang));

            return Command::SUCCESS;
        }

        $this->createListing();
        $flush = (bool)$input->getOption('flush');
        if ($flush) {
            $this->urnDefinitionRepository->flush();
        }

        return Command::SUCCESS;
    }

    private function createListing()
    {
        $urnsString = json_decode($this->pivotRemoteRepository->thesaurus('urn'), null, 512, JSON_THROW_ON_ERROR);

        $response = $this->pivotSerializer->deserializeToClass(
            json_encode($urnsString, JSON_THROW_ON_ERROR),
            UrnResponse::class
        );

        if ($response) {
            foreach ($response->spec as $urnDefinition) {
                $this->io->section($urnDefinition->labelByLanguage('fr'));
                $this->createUrn($urnDefinition);
            }
            $this->io->writeln(count($response->spec));
        } else {
            $this->io->error($urnsString);
        }
    }

    private function createUrn(UrnDefinition $urnDefinition): UrnDefinitionEntity
    {
        $urn = UrnDefinitionEntity::fromUrnDefinition($urnDefinition);
        $this->urnDefinitionRepository->persist($urn);

        return $urn;
    }
}
