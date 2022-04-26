<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Repository\PivotRemoteRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'pivot:offre-dump',
    description: 'Add a short description for your command',
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
            ->addArgument('codeCgt', InputArgument::OPTIONAL, 'code cgt');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $codeCgt = $input->getArgument('codeCgt');

        if (!$codeCgt) {
            $resultString = $this->pivotRemoteRepository->query();
        } else {
            $resultString = $this->pivotRemoteRepository->offreByCgt($codeCgt);
        }
        echo $resultString;
        $io->writeln("");

        return Command::SUCCESS;
    }
}
