<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Repository\PivotRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
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

    public function __construct(
        private PivotRepository $pivotRepository,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('type', InputArgument::OPTIONAL, 'Type offre', null)
            ->addOption('listing', "ll", InputOption::VALUE_NONE, 'Liste les types');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $this->io = new SymfonyStyle($input, $output);
        $typeSelected = $input->getArgument('type');

        if (!$typeSelected) {
            $response = $this->askType();
            if ($response) {
                $typeSelected = $this->catchResponseSelected($response);
            }
        } else {
            if (!$response = $this->catchTypeGiven($typeSelected)) {
                $this->io->error('Ce type n\'exite pas dans la liste');

                return Command::FAILURE;
            }
        }

        $this->io->success($response.": ");
        if ($typeSelected === 0) {
            $args = [];
        } else {
            $args = [$typeSelected];
        }

        $this->io->info("Chargement des offres...");
        $offres = $this->pivotRepository->getOffres($args);
        $count = count($offres);
        $this->io->info("$count offres trouvées");
        $rows = [];
        foreach ($offres as $offre) {
            $rows[] = [$offre->nom, $offre->codeCgt, $offre->dateModification];
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
        $typesOffre = $this->getAllTypes();

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

    private function getAllTypes(): array
    {
        //$this->io->info("Création du listing des types...");
        // $progressBar = new ProgressBar($this->output, 0);
        //  $progressBar->start();
        //  $progressBar->advance(30);
        $types = $this->pivotRepository->getTypesOffre();
        $types[0] = 'Tout';
        //  $progressBar->advance(70);
        //  $progressBar->finish();

        return $types;
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestArgumentValuesFor(argumentName: 'type')) {
            $suggestions->suggestValues($this->getAllTypes());
        }
    }

    private function catchResponseSelected(string $response): int
    {
        return array_search($response, $this->getAllTypes());
    }

    private function catchTypeGiven(int $typeGiven): ?string
    {
        return $this->getAllTypes()[$typeGiven] ?? null;
    }
}
