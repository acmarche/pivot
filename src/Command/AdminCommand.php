<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Utils\GenerateClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * bin/console pivot:admin > AcMarche/Pivot/src/Spec/UrnTypeList.php
 */
#[AsCommand(
    name: 'pivot:admin',
    description: 'Génère la class UrnTypeList.',
)]
class AdminCommand extends Command
{
    public function __construct(
        private readonly GenerateClass $generateClass,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->generateClass->generateTypeUrn();

        return Command::SUCCESS;
    }
}
