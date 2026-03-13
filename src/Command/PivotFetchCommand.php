<?php

declare(strict_types=1);

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
    name: 'pivot:fetch',
    description: 'Fetch offers from the Pivot API',
)]
class PivotFetchCommand extends Command
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
            ->addOption('level', 'l', InputOption::VALUE_REQUIRED, 'Content level (1-4)', 0)
            ->addOption('clear', null, InputOption::VALUE_NONE, 'Clear Pivot cache (files + Redis) before fetching')
            ->addOption('display', null, InputOption::VALUE_NONE, 'Display offers');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('clear')) {
            $this->pivotClient->clearCache();
            $io->info('Pivot Redis cache cleared. JSON files kept as fallback until fresh data is fetched.');
        }

        $display = $input->getOption('display');
        $code = $input->getArgument('code');
        $contentLevel = null;
        $level = (int)$input->getOption('level');
        if ($level > 0) {
            $contentLevel = ContentLevel::from($level);
        }

        try {
            if ($code) {
                $io->info(sprintf('Fetching offer: %s (level %d)', $code, $contentLevel->value));
                $response = $this->pivotClient->fetchOfferByCode($code, $contentLevel);

            } elseif ($contentLevel) {
                $io->info(sprintf('Fetching offers from query (level %d)', $contentLevel->value));
                $response = $this->pivotClient->fetchOffersByCriteria($contentLevel);
            } else {
                foreach (ContentLevel::cases() as $contentLevel) {
                    $io->info(sprintf('Fetching offers from query (level %d)', $contentLevel->value));
                    $response = $this->pivotClient->fetchOffersByCriteria($contentLevel);
                    unset($response);
                }

                return Command::SUCCESS;
            }

            $io->success(sprintf('Retrieved %d offer(s)', $response->count));

            if ($display) {
                foreach ($response->getOffers() as $offer) {
                    $io->section($offer->nom ?? 'Unnamed offer');
                    $io->listing([
                        sprintf('Code: %s', $offer->codeCgt),
                        sprintf('Type: %s', $offer->typeOffre?->getLabelByLang('fr') ?? 'N/A'),
                        sprintf('Active: %s', $offer->isActive() ? 'Yes' : 'No'),
                        sprintf('Visible: %s', $offer->isVisible() ? 'Yes' : 'No'),
                        sprintf('Address: %s', $offer->adresse1?->getFullAddress() ?? 'N/A'),
                        sprintf('Phone: %s', $offer->getPhone() ?? 'N/A'),
                        sprintf('Email: %s', $offer->getEmail() ?? 'N/A'),
                        sprintf('Website: %s', $offer->getWebsite() ?? 'N/A'),
                        sprintf('Specifications: %d', count($offer->spec)),
                        sprintf('Relations: %d', count($offer->relOffre)),
                        sprintf(
                            'Classifications: %s',
                            $offer->getClassificationLabels() !== [] ? implode(', ', $offer->getClassificationLabels()) : 'N/A'
                        ),
                    ]);

                    if ($offer->spec !== []) {
                        $rows = [];
                        foreach ($offer->spec as $spec) {
                            $rows[] = [
                                $spec->urn,
                                $spec->getLabelByLang('fr') ?? '',
                                $spec->urnCat,
                                $spec->urnCatLabel !== [] ? $spec->urnCatLabel[0]->value : '',
                                $spec->value ?? '',
                            ];
                        }
                        $io->table(['URN', 'Label', 'Category URN', 'Category', 'Value'], $rows);
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
