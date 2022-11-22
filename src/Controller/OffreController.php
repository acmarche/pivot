<?php

namespace AcMarche\Pivot\Controller;

use AcMarche\Pivot\Entity\TypeOffre;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Utils\SortUtils;
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
    ) {
    }

    #[Route(path: '/index/{id}', name: 'pivot_offres')]
    public function index(TypeOffre $typeOffre): Response
    {
        try {
            $json = $this->pivotRemoteRepository->offreByCgt($typeOffre->urn);
            $offres = $this->pivotRepository->fetchOffres([$typeOffre]);
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Erreur: '.$e->getMessage());

            return $this->redirectToRoute('pivot_typeoffre_index');
        }

        $offres = SortUtils::sortOffres($offres);

        return $this->render(
            '@AcMarchePivot/offres/index.html.twig',
            [
                'typeOffre' => $typeOffre,
                'offres' => $offres,
            ]
        );
    }

    #[Route(path: '/json/{codeCgt}', name: 'pivot_offre_json')]
    public function jsonAction(string $codeCgt): Response
    {
        $json = $this->pivotRemoteRepository->offreByCgt($codeCgt);

        return new Response($json);
    }
}
