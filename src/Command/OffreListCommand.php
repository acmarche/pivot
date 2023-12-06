<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entity\TypeOffre;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Repository\TypeOffreRepository;
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
        private readonly TypeOffreRepository $typeOffreRepository,
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
            try {
                $this->all();
                $this->displayOffres($this->offres, "Toutes les offres");

            } catch (\JsonException|InvalidArgumentException|\Exception $e) {
                $this->io->error($e->getMessage());
            }

            return Command::SUCCESS;
        }

        if ($events) {
            $offres = $this->pivotRepository->fetchEvents();
            $this->displayOffres($offres, "Events");

            return Command::SUCCESS;
        }

        if ($urn) {
            $typeOffre = $this->typeOffreRepository->findOneByUrn($urn);
            $offres = $this->pivotRepository->fetchOffres([$typeOffre]);
            $this->displayOffres($offres, $typeOffre->name);

            return Command::SUCCESS;
        }

        $response = $this->askType();
        if (!$response instanceof TypeOffre) {
            $this->io->error('Ce type n\'exite pas dans la liste');

            return Command::FAILURE;
        }

        $offres = $this->pivotRepository->fetchOffres([$response]);
        $this->displayOffres($offres, $response->name);

        return Command::SUCCESS;
    }

    protected function askType(): mixed
    {
        $typesOffre = $this->typeOffreRepository->findRoots();

        $choice = new ChoiceQuestion(
            question: 'Quelle type d\'offre ?',
            choices: $typesOffre
        );

        return $this->io->askQuestion($choice);
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestArgumentValuesFor(argumentName: 'type')) {
            $suggestions->suggestValues($this->typeOffreRepository->findRoots());
        }
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException|\JsonException
     * @throws \Exception
     */
    private function all(): void
    {
        $responseQuery = $this->pivotRepository->getAllDataFromRemote();

        foreach ($responseQuery->offre as $offreShort) {
            $offre = $this->pivotRepository->fetchOffreByCgt($offreShort->codeCgt, $offreShort->dateModification);
            if (!$offre instanceof Offre) {
                dd($offre);
            }
            $this->offres[] = $offre;
        }
        // $offres = FilterUtils::filterByTypeIdsOrUrns($this->offres, [], [$typeOffre->urn]);
    }

    private function displayOffres(array $offres, string $title): void
    {
        $count = count($offres);
        $this->io->section($title);
        $this->io->info("$count offres trouvées");
        $rows = [];
        foreach ($offres as $offre) {
            $rows[] = [$offre->name(), $offre->codeCgt, $offre->dateModification];
        }

        $table = new Table($this->output);
        $table
            ->setHeaders(['Nom', 'CodeCgt', 'Modifié le'])
            ->setRows($rows);
        $table->render();

        $this->io->info("Pour le détail d'une offre: bin/console pivot:offre-dump codeCgt");
    }
}
