<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Repository\PivotRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'pivot:cache-dump',
    description: 'Télécharge toutes les offres et les mets en cache',
)]
class CacheDumpCommand extends Command
{
    public function __construct(
        private PivotRepository $pivotRepository,
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

        $offres = $this->pivotRepository->getOffres([]);

        return Command::SUCCESS;
    }
}
