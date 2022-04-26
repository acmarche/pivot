<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Cache\CacheInterface;

#[AsCommand(
    name: 'pivot:offre-list',
    description: 'Add a short description for your command',
)]
class OffreListCommand extends Command
{
    private SymfonyStyle $io;
    private OutputInterface $output;

    public function __construct(
        private PivotRemoteRepository $pivotRemoteRepository,
        private PivotRepository $pivotRepository,
        private CacheInterface $cache,
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
        }
        else {
            if(!$response = $this->catchTypeSelected($typeSelected)){
                $this->io->error('Ce type n\'exite pas dans la liste');
                return Command::FAILURE;
            }
        }

        $this->io->success($response.": ");
        $offres = $this->pivotRepository->getOffres([$typeSelected]);
        dump($offres);

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

    protected function getAllTypes(): array
    {
        return $this->cache->get('pivote_list_types', function () {
            return $this->createLisiting();
        });
    }

    private function groupingOffres()
    {
        $groupe = [];

        if (!isset($groupe[$labelType])) {
            $groupe[$labelType] = 1;
        } else {
            $groupe[$labelType]++;
        }

    }

    private function createLisiting(): array
    {
        $progressBar = new ProgressBar($this->output, 0);
        $progressBar->start();

        $resultString = $this->pivotRemoteRepository->query();
        $progressBar->advance(30);

        $data = json_decode($resultString);

        $types = [];
        foreach ($data->offre as $offreInline) {
            $offreString = $this->pivotRemoteRepository->offreByCgt($offreInline->codeCgt);
            $offreObject = json_decode($offreString);
            $offre = $offreObject->offre[0];
            $type = $offre->typeOffre;
            $idType = $type->idTypeOffre;
            $labelType = $type->label[0]->value;
            $types[$idType] = $labelType;

            $progressBar->advance();
        }

        $progressBar->finish();

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

    private function catchTypeSelected(int $typeGiven): ?string
    {
        return $this->getAllTypes()[$typeGiven] ?? null;
    }
}
