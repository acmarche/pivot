<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Command;

use AcMarche\PivotAi\Cache\OfferStore;
use AcMarche\PivotAi\Cache\PivotCache;
use AcMarche\PivotAi\Enums\ContentLevel;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'pivot:index',
    description: 'Rebuild the SQLite offer index from the JSON archives in data/pivot/',
)]
class PivotIndexCommand extends Command
{
    public function __construct(
        private readonly PivotCache $pivotCache,
        private readonly OfferStore $offerStore,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('level', 'l', InputOption::VALUE_REQUIRED, 'Only rebuild this content level (0-4)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $levelOption = $input->getOption('level');
        $levels = $levelOption !== null
            ? [ContentLevel::from((int) $levelOption)]
            : ContentLevel::cases();

        $rows = [];
        foreach ($levels as $level) {
            $data = $this->pivotCache->get($level);
            if ($data === null) {
                $io->warning(sprintf(
                    'Level %d: no JSON archive at %s — run "pivot:fetch" first',
                    $level->value,
                    $this->pivotCache->getFilePath($level),
                ));
                $rows[] = [$level->value, $level->name, 'skipped', '-'];

                continue;
            }

            $start = microtime(true);

            try {
                $written = $this->offerStore->replaceLevel($level, $data);
            } catch (\Throwable $e) {
                $io->error(sprintf('Level %d: %s', $level->value, $e->getMessage()));

                return Command::FAILURE;
            } finally {
                unset($data);
            }

            $rows[] = [$level->value, $level->name, $written.' offers', sprintf('%.2fs', microtime(true) - $start)];
        }

        $io->table(['Level', 'Name', 'Indexed', 'Time'], $rows);
        $io->success(sprintf('Index rebuilt at %s', $this->offerStore->getDbPath()));

        return Command::SUCCESS;
    }
}
