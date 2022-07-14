<?php

namespace AcMarche\Pivot\Controller;

use AcMarche\Pivot\Parser\OffreParser;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route(path: '/offre')]
class OffreController extends AbstractController
{
    public function __construct(
        private PivotRepository $pivotRepository,
        private PivotRemoteRepository $pivotRemoteRepository,
        private OffreParser $pivotParser
    ) {
    }

    #[Route(path: '/{urn}', name: 'pivot_offres')]
    public function index(string $urn): Response
    {

        return $this->render(
            '@AcMarchePivot/default/index.html.twig',
            [

            ]
        );
    }


}
