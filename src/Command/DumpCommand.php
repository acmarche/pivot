<?php

namespace AcMarche\PivotAi\Command;

use AcMarche\PivotAi\Api\PivotClient;
use AcMarche\PivotAi\Enums\ContentLevel;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'pivot:dump',
    description: 'Dump an offer as serialized object or raw JSON',
)]
class DumpCommand extends Command
{
    public function __construct(
        private readonly PivotClient $pivotClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('code', InputArgument::OPTIONAL, 'Specific offer code (codeCgt) to fetch')
            ->addOption('level', 'l', InputOption::VALUE_REQUIRED, 'Content level (1-4)', ContentLevel::Full->value)
            ->addOption('raw', 'r', InputOption::VALUE_NONE, 'Dump raw JSON data instead of serialized offer');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $code = $input->getArgument('code');
        $raw = $input->getOption('raw');
        $contentLevel = null;
        $level = (int)$input->getOption('level');
        if ($level > 0) {
            $contentLevel = ContentLevel::from($level);
        }

        try {
            if ($code) {
                if ($raw) {
                    $data = $this->pivotClient->fetchFromApi($contentLevel ?? ContentLevel::Full);
                    foreach ($data['offre'] ?? [] as $offerData) {
                        if (($offerData['codeCgt'] ?? null) === $code) {
                            $io->writeln(json_encode($offerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

                            return Command::SUCCESS;
                        }
                    }
                    $io->warning(sprintf('No offer found for code "%s"', $code));
                } else {
                    $response = $this->pivotClient->fetchOfferByCode($code, $contentLevel);
                    if ($response === null || $response->offre === []) {
                        $io->warning(sprintf('No offer found for code "%s"', $code));

                        return Command::SUCCESS;
                    }
                    dump($response->offre[0]);
                }
            } else {
                if ($raw) {
                    $data = $this->pivotClient->fetchFromApi($contentLevel ?? ContentLevel::Full);
                    foreach ($data['offre'] ?? [] as $offerData) {
                        $io->writeln(json_encode($offerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                        $io->newLine();
                    }
                } else {
                    $response = $this->pivotClient->fetchOffersByCriteria($contentLevel);
                    $io->info(sprintf('Found %d offers', $response->count));

                    foreach ($response->offre as $offer) {
                        $io->section(sprintf('%s — %s', $offer->codeCgt, $offer->nom));
                        dump($offer);
                    }
                }
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error(sprintf('Failed to fetch offers: %s', $e->getMessage()));

            if ($output->isVerbose()) {
                $io->error($e->getTraceAsString());
            }

            return Command::FAILURE;
        }
    }
}
