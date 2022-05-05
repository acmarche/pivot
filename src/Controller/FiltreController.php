<?php

namespace AcMarche\Pivot\Controller;

use AcMarche\Pivot\Entity\Filtre;
use AcMarche\Pivot\Form\FiltreEditType;
use AcMarche\Pivot\Form\FiltreSearchType;
use AcMarche\Pivot\Repository\FiltreRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/filtre')]
#[IsGranted(data: 'ROLE_PIVOT')]
class FiltreController extends AbstractController
{
    public function __construct(private FiltreRepository $filterRepository)
    {
    }

    #[Route(path: '/', name: 'pivot_filtre_index')]
    public function index(Request $request): Response
    {
        $filters = $this->filterRepository->findWithChildren();
        $data = [];
        $form = $this->createForm(FiltreSearchType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $filters = $this->filterRepository->findByName($data['name']);

            return $this->render('@AcMarchePivot/filter/search.html.twig', [
                'filters' => $filters,
                'form' => $form->createView(),
            ]);
        }

        return $this->render('@AcMarchePivot/filter/index.html.twig', [
            'filters' => $filters,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/show', name: 'pivot_filtre_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Filtre $filter): Response
    {
        return $this->render(
            '@AcMarchePivot/filter/show.html.twig',
            [
                'filter' => $filter,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'pivot_filtre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Filtre $filter): Response
    {
        $form = $this->createForm(FiltreEditType::class, $filter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->filterRepository->flush();

            $this->addFlash('success', 'Le filtre a bien été modifié');

            return $this->redirectToRoute('pivot_filtre_show', [
                'id' => $filter->id,
            ]);
        }

        return $this->render(
            '@AcMarchePivot/filter/edit.html.twig',
            [
                'filter' => $filter,
                'form' => $form->createView(),
            ]
        );
    }
}
