<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use AcMarche\Pivot\Utils\CacheUtils;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

//todo https://symfony.com/doc/current/cache.html#clearing-the-cache
// https://symfony.com/doc/current/cache.html#encrypting-the-cache
#[AsCommand(
    name: 'pivot:cache',
    description: 'Manage cache',
)]
class CacheCommand extends Command
{
    public function __construct(
        private readonly PivotRepository $pivotRepository,
        private readonly TypeOffreRepository $typeOffreRepository,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addOption('generate', "generate", InputOption::VALUE_NONE, 'Generate');
        $this->addOption('purge', "purge", InputOption::VALUE_NONE, 'Purge');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $purge = (bool)$input->getOption('purge');
        $generate = (bool)$input->getOption('generate');

        if ($generate) {
            try {
                $this->allOffersShort();
                $this->pivotRepository->fetchEvents();
                foreach ($this->typeOffreRepository->findAll() as $typeOffre) {
                    try {
                        $this->pivotRepository->fetchOffres([$typeOffre], true);
                    } catch (InvalidArgumentException $e) {
                        $io->error($e->getMessage());
                    }
                }
                //$io->success('Cache generated');
            } catch (\JsonException|InvalidArgumentException|\Exception $e) {
                $io->error($e->getMessage());
            }

            return Command::SUCCESS;
        }

        if ($purge) {
            try {
                $cacheUtils = new CacheUtils();
                $cache = $cacheUtils->instance();
                $cache->invalidateTags(CacheUtils::TAG);
                //$io->success('Cache cleaned');
            } catch (InvalidArgumentException $e) {
                $io->error($e->getMessage());
            }
        }

        if (!$purge && !$generate) {
            $io->warning('Use --generate or --purge');
        }

        return Command::SUCCESS;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException|\JsonException
     * @throws \Exception
     */
    private function allOffersShort(): void
    {
        $responseQuery = $this->pivotRepository->getAllDataFromRemote();

        foreach ($responseQuery->offre as $offreShort) {
            $this->pivotRepository->fetchOffreByCgt($offreShort->codeCgt, $offreShort->dateModification);
        }
    }
}
