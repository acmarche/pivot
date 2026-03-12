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
    name: 'pivot:compare-levels',
    description: 'Compare an offer across the 4 content levels from cached data',
)]
class PivotCompareLevelsCommand extends Command
{
    public function __construct(
        private readonly PivotClient $pivotClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('code', InputArgument::REQUIRED, 'The codeCgt of the offer to compare (e.g. RST-01-08H3-187H)')
            ->addOption('relations', 'r', InputOption::VALUE_NONE, 'Show detailed info for each relOffre (nested offer specs, name, type, address)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $codeCgt = $input->getArgument('code');

        $io->title(sprintf('Comparing offer "%s" across 4 content levels', $codeCgt));

        $offers = [];
        foreach (ContentLevel::cases() as $level) {
            $offer = $this->pivotClient->loadOffer($codeCgt, $level);
            if ($offer === null) {
                $io->warning(sprintf('Offer not found in cache for level %d (%s). Run "pivot:fetch --level=%d" first.', $level->value, $level->name, $level->value));
                continue;
            }
            $offers[$level->value] = $offer;
        }

        if ($offers === []) {
            $io->error('Offer not found in any cached level.');
            return Command::FAILURE;
        }

        // General info table
        $io->section('General Info');
        $rows = [];
        foreach ($offers as $levelValue => $offer) {
            $level = ContentLevel::from($levelValue);
            $rows[] = [
                sprintf('%d (%s)', $level->value, $level->name),
                $offer->nom ?? 'N/A',
                $offer->typeOffre?->getLabelByLang('fr') ?? 'N/A',
                $offer->isActive() ? 'Yes' : 'No',
                $offer->isVisible() ? 'Yes' : 'No',
                count($offer->spec),
                count($offer->relOffre),
                count($offer->relOffreTgt),
            ];
        }
        $io->table(
            ['Level', 'Name', 'Type', 'Active', 'Visible', 'Specs', 'Relations', 'Rel Targets'],
            $rows,
        );

        // Address comparison
        $io->section('Address');
        $rows = [];
        foreach ($offers as $levelValue => $offer) {
            $level = ContentLevel::from($levelValue);
            $addr = $offer->adresse1;
            $rows[] = [
                sprintf('%d (%s)', $level->value, $level->name),
                $addr?->getFullAddress() ?? 'N/A',
                $addr?->cp ?? '',
                $addr?->latitude !== null ? sprintf('%.5f, %.5f', $addr->latitude, $addr->longitude) : 'N/A',
            ];
        }
        $io->table(['Level', 'Address', 'Zip', 'Lat/Lng'], $rows);

        // Communication
        $io->section('Communication');
        $rows = [];
        foreach ($offers as $levelValue => $offer) {
            $level = ContentLevel::from($levelValue);
            $rows[] = [
                sprintf('%d (%s)', $level->value, $level->name),
                $offer->getPhone() ?? '-',
                $offer->getEmail() ?? '-',
                $offer->getWebsite() ?? '-',
            ];
        }
        $io->table(['Level', 'Phone', 'Email', 'Website'], $rows);

        // Spec URN comparison
        $io->section('Specifications (URNs) by level');
        $allUrns = [];
        foreach ($offers as $levelValue => $offer) {
            foreach ($offer->spec as $spec) {
                $allUrns[$spec->urn] ??= [];
                $allUrns[$spec->urn][$levelValue] = $spec->value;
            }
        }
        ksort($allUrns);

        $levels = array_keys($offers);
        $headerRow = ['URN'];
        foreach ($levels as $l) {
            $headerRow[] = 'L'.$l;
        }
        $headerRow[] = 'Value (first available)';

        $rows = [];
        foreach ($allUrns as $urn => $perLevel) {
            $row = [$urn];
            $firstValue = null;
            foreach ($levels as $l) {
                if (isset($perLevel[$l])) {
                    $row[] = 'X';
                    $firstValue ??= $perLevel[$l];
                } else {
                    $row[] = '-';
                }
            }
            $displayValue = $firstValue ?? '';
            if (strlen($displayValue) > 60) {
                $displayValue = substr($displayValue, 0, 57).'...';
            }
            $row[] = $displayValue;
            $rows[] = $row;
        }
        $io->table($headerRow, $rows);

        // Relations comparison
        $showRelationDetails = $input->getOption('relations');
        $io->section('Relations (relOffre)');
        foreach ($offers as $levelValue => $offer) {
            $level = ContentLevel::from($levelValue);
            if ($offer->relOffre === []) {
                $io->text(sprintf('Level %d (%s): no relations', $level->value, $level->name));
                continue;
            }
            $io->text(sprintf('Level %d (%s): %d relation(s)', $level->value, $level->name, count($offer->relOffre)));
            foreach ($offer->relOffre as $rel) {
                $io->text(sprintf('  - [%s] %s (specs: %d)', $rel->urn ?? '?', $rel->offre?->codeCgt ?? '?', count($rel->offre?->spec ?? [])));

                if ($showRelationDetails && $rel->offre !== null) {
                    $relOffer = $rel->offre;
                    $io->text(sprintf('      Name: %s', $relOffer->nom ?? '-'));
                    $io->text(sprintf('      Type: %s', $relOffer->typeOffre?->getLabelByLang('fr') ?? '-'));
                    $io->text(sprintf('      Address: %s', $relOffer->adresse1?->getFullAddress() ?? '-'));
                    if ($relOffer->spec !== []) {
                        $io->text('      Specs:');
                        foreach ($relOffer->spec as $spec) {
                            $val = $spec->value ?? '';
                            if (strlen($val) > 80) {
                                $val = substr($val, 0, 77).'...';
                            }
                            $io->text(sprintf('        [%s] %s = %s', $spec->type ?? '?', $spec->urn ?? '?', $val));
                        }
                    }
                    if ($relOffer->relOffre !== []) {
                        $io->text(sprintf('      Nested relations: %d', count($relOffer->relOffre)));
                    }
                }
            }
        }

        $io->newLine();
        $io->success('Comparison complete.');

        return Command::SUCCESS;
    }
}
