<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;

#[AsCommand(
    name: 'pivot:offre-dump',
    description: 'Add a short description for your command',
)]
class OffreDumpCommand extends Command
{
    public function __construct(
        private SerializerInterface $serializer,
        private PivotRepository $pivotRepository,
        private PivotRemoteRepository $pivotRemoteRepository,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('codeCgt', InputArgument::REQUIRED, 'code cgt');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $codeCgt = $input->getArgument('codeCgt');

        if (!$codeCgt) {
            $io->note(sprintf('Code cgt requis: %s', $codeCgt));

            return Command::FAILURE;
        }

        $hotelString = $this->pivotRemoteRepository->offreByCgt($codeCgt);
        echo $hotelString;

        $io->writeln("");

        return Command::SUCCESS;
    }
}
