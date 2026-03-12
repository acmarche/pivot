<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Command;

use AcMarche\PivotAi\Api\PivotClient;
use AcMarche\PivotAi\Api\ThesaurusClient;
use AcMarche\PivotAi\Entity\Pivot\Specification;
use AcMarche\PivotAi\Enums\ContentLevel;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'pivot:thesaurus:fetch',
    description: 'Fetch and cache URN labels from the Pivot thesaurus API',
)]
class ThesaurusFetchCommand extends Command
{
    public function __construct(
        private readonly PivotClient $pivotClient,
        private readonly ThesaurusClient $thesaurusClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('clear', null, InputOption::VALUE_NONE, 'Clear thesaurus cache before fetching')
            ->addOption('level', 'l', InputOption::VALUE_REQUIRED, 'Content level to scan for URNs (0-4)', ContentLevel::Full->value);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('clear')) {
            $this->thesaurusClient->clearCache();
            $io->info('Thesaurus caches cleared (files + Redis).');
        }

        $level = ContentLevel::from((int) $input->getOption('level'));

        $io->info(sprintf('Loading offers at level %d to collect URNs...', $level->value));

        try {
            $response = $this->pivotClient->fetchOffersByCriteria($level);
        } catch (\Throwable $e) {
            $io->error(sprintf('Failed to load offers: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $urns = [];
        foreach ($response->getOffers() as $offer) {
            foreach ($offer->spec as $spec) {
                $this->collectUrns($spec, $urns);
            }
        }

        $uniqueUrns = array_values(array_unique($urns));
        $io->info(sprintf('Found %d unique URNs to fetch from thesaurus.', count($uniqueUrns)));

        $fetched = $this->thesaurusClient->fetchUrns($uniqueUrns);

        $cache = $this->thesaurusClient->loadCache();
        $io->success(sprintf('Fetched %d new URN(s). Total cached: %d.', $fetched, count($cache)));

        return Command::SUCCESS;
    }

    /**
     * @param string[] $urns
     */
    private function collectUrns(Specification $spec, array &$urns): void
    {
        if ($spec->urn !== null) {
            $urns[] = $spec->getBaseUrn();
        }

        if ($spec->value !== null && $spec->isValueUrn()) {
            $urns[] = $spec->value;
        }

        if ($spec->urnCat !== null) {
            $urns[] = $spec->urnCat;
        }

        if ($spec->urnSubCat !== null) {
            $urns[] = $spec->urnSubCat;
        }

        foreach ($spec->spec as $nestedSpec) {
            $this->collectUrns($nestedSpec, $urns);
        }
    }
}
