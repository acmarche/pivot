<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Entities\Pivot\TypeOffre;
use AcMarche\Pivot\Pivot;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Utils\FileUtils;
use AcMarche\Pivot\Utils\SerializerPivot;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LoaderCommand extends Command
{
    protected static $defaultName = 'pivot:loadxml';
    private SymfonyStyle $io;
    private PivotRemoteRepository $pivotRemoteRepository;

    protected function configure(): void
    {
        $this
            ->setDescription('Charge le xml de hades');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $today = date('Y-m-d');

        /*    $result = $this->getLastSync();
            if ($result['date'] == $today && $result['result'] == 'true') {
                return Command::SUCCESS;
            }*/

        $this->pivotRemoteRepository = new PivotRemoteRepository();
        $this->getTypes();

        $query = file_get_contents('/var/www/visit/AcMarche/Pivot/Query/test.xml');
        // $pivotRemoteRepository->search($query);

        //cho($jsonString);

        return Command::SUCCESS;
    }

    private function getTypes()
    {
        $jsonString = $this->pivotRemoteRepository->getThesaurus(Pivot::THESAURUS_TYPE_OFFRE);

        $serializer = SerializerPivot::create();
        $titi = $serializer->deserialize($jsonString, 'AcMarche\Pivot\Entities\Pivot\Titi', 'json', [

        ]);

        foreach ($titi->spec as $type) {
          //  var_dump($type);
            break;
            //echo $type['code'];
        }

        var_dump($serializer->deserialize(file_get_contents('/var/www/visit/one.json'), TypeOffre::class, 'json'));

        //echo($jsonString);
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