<?php

namespace AcMarche\Pivot\Controller;

use AcMarche\Pivot\Entity\TypeOffre;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use AcMarche\Pivot\Utils\SortUtils;
use Doctrine\ORM\NonUniqueResultException;
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
        private TypeOffreRepository $typeOffreRepository,
    ) {
    }

    #[Route(path: '/{id}/{urn}', name: 'pivot_offres')]
    public function index(TypeOffre $typeOffre, string $urn): Response
    {
        try {
            $typeOffreUrn = $this->typeOffreRepository->findByUrn($urn);
        } catch (NonUniqueResultException $e) {
            $typeOffreUrn = $typeOffre;
        }
        $offres = $this->pivotRepository->getOffres([$typeOffre]);
        $offres = SortUtils::sortOffres($offres);

        return $this->render(
            '@AcMarchePivot/offres/index.html.twig',
            [
                'urn' => $urn,
                'typeOffre' => $typeOffre,
                'typeOffreUrn' => $typeOffreUrn,
                'offres' => $offres,
            ]
        );
    }
}
