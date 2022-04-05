<?php

namespace AcMarche\Pivot\Controller;

use AcMarche\Pivot\Parser\PivotParser;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Spec\SpecEvent;
use AcMarche\Pivot\Spec\UrnConst;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: '/')]
class DefaultController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private PivotRepository $pivotRepository,
        private PivotParser $pivotParser
    ) {
    }

    #[Route(path: '/', name: 'pivot_home')]
    public function index(): Response
    {
        $events = $this->pivotRepository->getEvents();
        $this->pivotParser->parseEvents($events);

        return $this->render(
            '@AcMarchePivot/default/index.html.twig',
            [
                'events' => $events,
            ]
        );
    }

    private function events(array $events)
    {
        $offres = [];
        foreach ($events as $offre) {
            $this->io->writeln($offre->codeCgt);
            $this->io->writeln($offre->nom);
            $this->io->writeln($offre->typeOffre->labelByLanguage());
            $address = $offre->adresse1;
            $this->io->writeln(" ".$address->localiteByLanguage());
            $this->io->writeln(" ".$address->communeByLanguage());
            foreach ($offre->relOffre as $relation) {
                dump($relation);
                $item = $relation->offre;
                $code = dump($item['codeCgt']);
                $idType = $item['typeOffre']['idTypeOffre'];
                dump($idType);
                $sOffre = $this->pivotRepository->offreByCgt($code, $item['dateModification']);
                $itemSpec = new SpecEvent($sOffre->getOffre()->spec);
                dump($itemSpec->getByUrn(UrnConst::URL));
                dump($sOffre->getOffre()->nom);
            }
        }
    }
}
