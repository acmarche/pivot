<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Repository\HadesRemoteRepository;
use AcMarche\Pivot\Utils\FileUtils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class LoaderCommand extends Command
{
    protected static $defaultName = 'hades:loadxml';
    private SymfonyStyle $io;

    protected function configure(): void
    {
        $this
            ->setDescription('Charge le xml de hades');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $today = date('Y-m-d');

        $result = $this->getLastSync();
        if ($result['date'] == $today && $result['result'] == 'true') {
            return Command::SUCCESS;
        }

        $hadesRepository = new HadesRemoteRepository();
        $data = ['date' => $today];
        try {
         $jsonString =   $hadesRepository->loadOffres([]);
            $data['result'] = 'true';
        } catch (ClientExceptionInterface|ServerExceptionInterface|TransportExceptionInterface|RedirectionExceptionInterface|\Exception $e) {
            $data['result'] = 'false';
            $data['error'] = $e->getMessage();
        }

        file_put_contents(FileUtils::FILE_NAME_LOG, implode($data));

        return Command::SUCCESS;
    }

    private function getLastSync(): array
    {
        if (is_readable(FileUtils::FILE_NAME_LOG)) {
            try {
                return explode(',', file_get_contents(FileUtils::FILE_NAME_LOG));
            } catch (\Exception $exception) {

            }
        }

        return [];
    }

}