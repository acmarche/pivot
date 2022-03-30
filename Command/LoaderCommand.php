<?php

namespace AcMarche\Pivot\Command;

use AcMarche\Pivot\Entities\Person;
use AcMarche\Pivot\Entities\Pivot\Result\ResultAll;
use AcMarche\Pivot\Entities\Pivot\Result\ResultOfferDetail;
use AcMarche\Pivot\Entities\Pivot\Result\TypeOffreResult;
use AcMarche\Pivot\Pivot;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Utils\FileUtils;
use AcMarche\Pivot\Utils\SerializerPivot;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;

class LoaderCommand extends Command
{
    protected static $defaultName = 'pivot:loadxml';
    private SymfonyStyle $io;
    private PivotRemoteRepository $pivotRemoteRepository;
    private SerializerInterface $serializer;

    public function __construct(string $name = null)
    {
        $this->serializer = SerializerPivot::create();
        $this->pivotRemoteRepository = new PivotRemoteRepository(Pivot::FORMAT_JSON);
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Charge le xml de hades');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->test();

        return Command::SUCCESS;
    }

    private function test()
    {
        $person = new Person();
        $person->setName('foo');
        $person->setAge(99);
        $person->setSportsperson(false);
        $json = json_encode($person);
        var_dump($json);
        $jsonContent = $this->serializer->deserialize($json, Person::class, 'json');
        dump($jsonContent);
    }

    private function search()
    {
        $query = file_get_contents('/var/www/visit/AcMarche/Pivot/Query/test.xml');
        var_dump($this->pivotRemoteRepository->queryPost($query));
        try {
            var_dump($this->pivotRemoteRepository->queryPost($query));
        } catch (\Exception $exception) {
            echo $exception->getMessage()."\n";
        }
    }

    private function detailOffre()
    {
        $hotel = 'HTL-01-08GR-01AY';
        $hotelString = $this->pivotRemoteRepository->offreByCgt($hotel);
        dump($this->serializer->deserialize($hotelString, ResultOfferDetail::class, 'json'));
    }

    private function all()
    {
        $hotelString = $this->pivotRemoteRepository->query();
        //dump($this->serializer->deserialize($hotelString, ResultAll::class, 'json'));
        echo $hotelString;
    }

    private function getTypes()
    {
        $jsonString = $this->pivotRemoteRepository->getThesaurus(Pivot::THESAURUS_TYPE_OFFRE);
        $titi = $this->serializer->deserialize($jsonString, TypeOffreResult::class, 'json', [

        ]);
        foreach ($titi->spec as $type) {
            var_dump($type);
            break;
            //echo $type['code'];
        }
        //   var_dump($serializer->deserialize(file_get_contents('/var/www/visit/one.json'), TypeOffre::class, 'json'));
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

        //calling :
        $today = date('Y-m-d');

        $result = $this->getLastSync();
        if ($result['date'] == $today && $result['result'] == 'true') {
            return Command::SUCCESS;
        }

        return [];
    }

}