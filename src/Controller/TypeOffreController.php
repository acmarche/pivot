<?php

namespace AcMarche\Pivot\Controller;

use AcMarche\Pivot\Entity\TypeOffre;
use AcMarche\Pivot\Form\TypeOffreEditType;
use AcMarche\Pivot\Form\TypeOffreSearchType;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/typeoffre')]
#[IsGranted(data: 'ROLE_PIVOT')]
class TypeOffreController extends AbstractController
{
    public function __construct(private TypeOffreRepository $typeOffreRepository)
    {
    }

    #[Route(path: '/', name: 'pivot_typeoffre_index')]
    public function index(Request $request): Response
    {
        $typesoffre = $this->typeOffreRepository->findWithChildren();
        $data = [];
        $form = $this->createForm(TypeOffreSearchType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $typesoffre = $this->typeOffreRepository->findByName($data['name']);

            return $this->render('@AcMarchePivot/typeoffre/search.html.twig', [
                'typesOffre' => $typesoffre,
                'form' => $form->createView(),
            ]);
        }

        return $this->render('@AcMarchePivot/typeoffre/index.html.twig', [
            'typesOffre' => $typesoffre,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/show', name: 'pivot_typeoffre_show', methods: ['GET', 'POST'])]
    public function show(Request $request, TypeOffre $typeoffre): Response
    {
        return $this->render(
            '@AcMarchePivot/typeoffre/show.html.twig',
            [
                'typeOffre' => $typeoffre,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'pivot_typeoffre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeOffre $typeoffre): Response
    {
        $form = $this->createForm(TypeOffreEditType::class, $typeoffre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->typeOffreRepository->flush();

            $this->addFlash('success', 'Le type a bien été modifié');

            return $this->redirectToRoute('pivot_typeoffre_show', [
                'id' => $typeoffre->id,
            ]);
        }

        return $this->render(
            '@AcMarchePivot/typeoffre/edit.html.twig',
            [
                'typeOffre' => $typeoffre,
                'form' => $form->createView(),
            ]
        );
    }
}
