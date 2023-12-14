<?php

namespace AcMarche\Pivot\Controller;

use AcMarche\Pivot\Api\QueryDetailEnum;
use Exception;
use AcMarche\Pivot\Entity\TypeOffre;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\TypeOffre\FilterUtils;
use AcMarche\Pivot\Utils\SortUtils;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route(path: '/offre')]
class OffreController extends AbstractController
{
    public function __construct(
        private readonly PivotRepository $pivotRepository,
        private readonly PivotRemoteRepository $pivotRemoteRepository,
    ) {
    }

    #[Route(path: '/all/', name: 'pivot_offres_all')]
    public function all(): Response
    {
        try {
            $responseJson = $this->pivotRepository->getAllDataFromRemote(
                true,
                QueryDetailEnum::QUERY_DETAIL_LVL_RESUME
            );
        } catch (Exception|InvalidArgumentException $e) {
            $this->addFlash('danger', 'Erreur: '.$e->getMessage());

            return $this->redirectToRoute('pivot_typeoffre_index');
        }

        $tmp = json_decode($responseJson)->offre;
        $offres = [];
        foreach ($tmp as $offre) {
            $std = new \stdClass();
            $std->codeCgt = $offre->codeCgt;
            $std->name = $offre->nom;
            $std->type = $offre->typeOffre->label[0]->value;
            $offres[] = $std;
        }
        $offres = SortUtils::sortOffresByName($offres);

        return $this->render(
            '@AcMarchePivot/offres/all.html.twig',
            [
                'offres' => $offres,
            ]
        );
    }

    #[Route(path: '/index/{id}', name: 'pivot_offres')]
    public function index(TypeOffre $typeOffre): Response
    {
        try {
            $offres = $this->pivotRepository->fetchOffres([$typeOffre]);
        } catch (Exception|InvalidArgumentException $e) {
            $this->addFlash('danger', 'Erreur: '.$e->getMessage());

            return $this->redirectToRoute('pivot_typeoffre_index');
        }

        $path = FilterUtils::getTypeOffrePath($typeOffre);
        $offres = SortUtils::sortOffres($offres);

        return $this->render(
            '@AcMarchePivot/offres/index.html.twig',
            [
                'typeOffre' => $typeOffre,
                'offres' => $offres,
                'path' => $path,
            ]
        );
    }

    #[Route(path: '/json/{codeCgt}', name: 'pivot_offre_json')]
    public function offreByCgt(string $codeCgt): Response
    {
        $json = $this->pivotRemoteRepository->offreByCgt($codeCgt);

        return new Response($json);
    }
}
