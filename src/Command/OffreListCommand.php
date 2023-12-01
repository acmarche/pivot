<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entity\TypeOffre;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use AcMarche\Pivot\Serializer\PivotSerializer;
use Doctrine\ORM\NonUniqueResultException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'pivot:offre-list',
    description: 'Liste les offres suivant un type choisis',
)]
class OffreListCommand extends Command
{
    private SymfonyStyle $io;
    private OutputInterface $output;
    private array $offres = [];

    public function __construct(
        private readonly PivotRepository $pivotRepository,
        private readonly PivotRemoteRepository $pivotRemoteRepository,
        private readonly TypeOffreRepository $typeOffreRepository,
        private readonly PivotSerializer $pivotSerializer,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addOption('all', "all", InputOption::VALUE_NONE, 'Toutes les offres');
        $this->addOption('events', "events", InputOption::VALUE_NONE, 'Toutes les events');
        $this->addOption('urn', "urn", InputOption::VALUE_OPTIONAL, 'Urn');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $this->io = new SymfonyStyle($input, $output);

        $all = (bool)$input->getOption('all');
        $events = (bool)$input->getOption('events');
        $urn = $input->getOption('urn');

        if ($all) {
            $choix = "Tout";
            try {
                $this->all();
            } catch (\JsonException|InvalidArgumentException $e) {
                $this->io->error($e->getMessage());
            }

            return Command::SUCCESS;
        }

        if ($urn) {
            $typeOffre = $this->typeOffreRepository->findOneByUrn($urn);
            $offres = $this->pivotRepository->fetchOffres([$typeOffre]);
            $this->io->info($typeOffre->name);
            $rows = [];
            foreach ($offres as $offre) {
                $rows[] = [$offre->name(), $offre->codeCgt, $offre->dateModification];
            }
            $table = new Table($output);
            $table
                ->setHeaders(['Nom', 'CodeCgt', 'Modifié le'])
                ->setRows($rows);
            $table->render();

            return Command::SUCCESS;
        }

        $response = $this->askType();
        if (!$response instanceof TypeOffre) {
            $this->io->error('Ce type n\'exite pas dans la liste');

            return Command::FAILURE;
        }
        $choix = $response->name;

        $this->io->success($choix.": ");

        $this->io->info("Chargement des offres...");
        $offres = $this->pivotRepository->fetchEvents(true);
        $count = count($offres);
        $this->io->info("$count offres trouvées");
        $rows = [];
        foreach ($offres as $offre) {
            $rows[] = [$offre->name(), $offre->codeCgt, $offre->dateModification];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Nom', 'CodeCgt', 'Modifié le'])
            ->setRows($rows);
        $table->render();

        $this->io->info("Pour le détail d'une offre: bin/console pivot:offre-dump codeCgt");

        return Command::SUCCESS;
    }

    protected function askType()
    {
        $typesOffre = $this->typeOffreRepository->findRoots();

        $choice = new ChoiceQuestion(
            question: 'Quelle type d\'offre ?',
            choices: $typesOffre
        );

        return $this->io->askQuestion($choice);
    }

    private function groupingOffres(array $offres)
    {
        $groupe = [];
        foreach ($offres as $offre) {
            $type = $offre->typeOffre;
            $idType = $type->idTypeOffre;
            $labelType = $type->label[0]->value;
            $types[$idType] = $labelType;
            if (!isset($groupe[$labelType])) {
                $groupe[$labelType] = 1;
            } else {
                $groupe[$labelType]++;
            }
        }
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestArgumentValuesFor(argumentName: 'type')) {
            $suggestions->suggestValues($this->typeOffreRepository->findRoots());
        }
    }

    /**
     * @return int|void
     * @throws \Psr\Cache\InvalidArgumentException|\JsonException
     */
    private function all()
    {
        $responseQuery = $this->pivotRepository->getAllDataFromRemote();
        foreach ($responseQuery->offre as $offreShort) {
            $dataString = $this->pivotRemoteRepository->offreByCgt($offreShort->codeCgt);
            $tmp = json_decode($dataString, null, 512, JSON_THROW_ON_ERROR);
            $dataStringOffre = json_encode($tmp->offre[0], JSON_THROW_ON_ERROR);

            $object = $this->pivotSerializer->deserializeToClass($dataStringOffre, Offre::class);
            if ($object) {
                $object->dataRaw = $dataString;
            }
            $this->offres[] = $object;
        }
        // $offres = FilterUtils::filterByTypeIdsOrUrns($this->offres, [], [$typeOffre->urn]);
    }
}
