<?php

namespace AcMarche\Pivot\Controller;

use AcMarche\Pivot\Entities\Person;
use AcMarche\Pivot\Jf;
use AcMarche\Pivot\Parser\PivotParser;
use AcMarche\Pivot\Repository\PivotRepository;
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
        private PivotParser $pivotParser,
        private Jf $jf
    ) {
    }

    #[Route(path: '/', name: 'pivot_home')]
    public function index(): Response
    {
       dd($this->jf->test());
        $this->pivotRepository->offreByCgt("EVT-A1-0016-2D6U");
        dd(123);

        return $this->render(
            '@AcMarchePivot/default/index.html.twig',
            [

            ]
        );
    }

    #[Route(path: '/events', name: 'pivot_events')]
    public function events(): Response
    {
        $events = $this->pivotRepository->getEvents();
        $this->pivotParser->parseEvents($events);

        return $this->render(
            '@AcMarchePivot/event/index.html.twig',
            [
                'events' => $events,
            ]
        );
    }

    #[Route(path: '/hotels', name: 'pivot_hotels')]
    public function hotel(): Response
    {
        $hotels = $this->pivotRepository->getHotels();
        $this->pivotParser->parseHotels($hotels);

        dump($hotels);

        //$this->pivotParser->parseEvents($events);

        return $this->render(
            '@AcMarchePivot/hebergement/hotels.html.twig',
            [
                'hotels' => $hotels,
            ]
        );
    }
}
