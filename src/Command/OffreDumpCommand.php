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
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'pivot:offre-dump',
    description: 'Affichage d\'une offre suivant le code cgt',
)]
class OffreDumpCommand extends Command
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
            ->addArgument('codeCgt', InputArgument::REQUIRED, 'code cgt', null)
            ->addOption('raw', "raw", InputOption::VALUE_OPTIONAL, 'display raw offre', true);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $codeCgt = $input->getArgument('codeCgt');
        $raw = $input->getOption('raw');

        $resultString = $this->pivotRemoteRepository->offreByCgt($codeCgt);

        if (!$raw) {
            echo $resultString;
            $io->writeln("");

            return Command::SUCCESS;
        }

        $offreObject = json_decode($resultString);
        $offre = $offreObject->offre[0];
        $type = $offre->typeOffre;
        $idType = $type->idTypeOffre;
        $labelType = $type->label[0]->value;
        $io->write($offre->nom);
        $io->write(" -- ".$idType);
        $io->writeln(" -- ".$labelType);

        $io->writeln("");

        return Command::SUCCESS;
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestArgumentValuesFor(argumentName: 'someArgument')) {
            $suggestions->suggestValues(['someSuggestion', 'otherSuggestion']);
        }
    }
}
