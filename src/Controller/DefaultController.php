<?php

namespace AcMarche\Pivot\Controller;

use AcMarche\Pivot\Parser\OffreParser;
use AcMarche\Pivot\Repository\PivotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route(path: '/')]
class DefaultController extends AbstractController
{
    public function __construct(
        private readonly PivotRepository $pivotRepository,
        private readonly OffreParser $pivotParser
    ) {
    }

    #[Route(path: '/', name: 'pivot_home')]
    public function index(): Response
    {
        return $this->render(
            '@AcMarchePivot/default/index.html.twig',
            [

            ]
        );
    }

    #[Route(path: '/events', name: 'pivot_events')]
    public function events(): Response
    {
        $events = $this->pivotRepository->fetchEvents();
        array_map(function ($event) {
            $this->pivotParser->parseDatesEvent($event);
        }, $events);

        return $this->render(
            '@AcMarchePivot/event/index.html.twig',
            [
                'events' => $events,
            ]
        );
    }

    #[Route(path: '/offres', name: 'pivot_all_offres')]
    public function offres(): Response
    {
        $hotels = [];

        //$this->pivotParser->parseEvents($events);

        return $this->render(
            '@AcMarchePivot/hebergement/hotels.html.twig',
            [
                'hotels' => $hotels,
            ]
        );
    }
}
