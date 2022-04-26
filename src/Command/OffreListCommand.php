<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Repository\PivotRemoteRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'pivot:offre-list',
    description: 'Add a short description for your command',
)]
class OffreListCommand extends Command
{
    public function __construct(
        private PivotRemoteRepository $pivotRemoteRepository,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('type', InputArgument::OPTIONAL, 'code cgt', null)
            ->addOption('listing', "ll", InputOption::VALUE_NONE, 'display nom offre');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $type = $input->getArgument('type');
        $listing = (bool)$input->getOption('listing');

        $resultString = $this->pivotRemoteRepository->query();

        $response = $this->listing($io);

        $io->writeln($response);

        return Command::SUCCESS;
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestArgumentValuesFor(argumentName: 'someArgument')) {
            $suggestions->suggestValues(['someSuggestion', 'otherSuggestion']);
        }
    }

    protected function listing(SymfonyStyle $io)
    {
        $rows = iterator_to_array($this->tagsTableRows());
        $table = $io->createTable();
        $table->setHeaderTitle("Interactive selection table example");
        $table->setRows($rows);
        $table->render();
        $io->newLine();

        $choice = new ChoiceQuestion(
            question: 'Which selection you choose?',
            choices: array_reduce(
                array: $rows,
                callback: function ($carry, $item) {
                    $carry[] = $item[0];

                    return $carry;
                }
            )
        );

        return $io->askQuestion($choice);
    }

    protected function tagsTableRows(): \Generator
    {
        $tags = [
            "Guide touristique" => 3,
            "Découverte et Divertissement" => 223,
            "Point d’intérêt" => 11,
            "Hôtel" => 3,
            "Restauration" => 62,
            "Gîte" => 12,
            "Événement" => 78,
            "Meublé" => 7,
            "Produit de terroir" => 79,
            "Chambre d’hôtes" => 10,
            "Hébergements" => 5,
            "Itinéraire" => 31,
            "Producteur" => 20,
            "Structure événementielle" => 3,
            "Boutique de terroir" => 17,
            "Artisan" => 5,
            "Organisme touristique" => 2,
            "MICE - Infrastructure" => 5,
            "MICE - Prestataire" => 2,
            "MICE - Animation" => 5,
            "MICE - Organisateur" => 2,
        ];

        foreach ($tags as $name => $key) {
            yield [$name, $key];
        }
    }
}
