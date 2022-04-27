<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Utils\GenerateClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'pivot:admin',
    description: 'Add a short description for your command',
)]
class AdminCommand extends Command
{
    private SymfonyStyle $io;
    private OutputInterface $output;

    public function __construct(
        private GenerateClass $generateClass,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
       $this->generateClass->generateTypeUrn();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);


        return Command::SUCCESS;
    }


}
