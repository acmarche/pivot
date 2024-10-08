<?php

namespace AcMarche\Pivot\Controller;

use AcMarche\Pivot\Entity\TypeOffre;
use AcMarche\Pivot\Form\TypeOffreEditType;
use AcMarche\Pivot\Form\TypeOffreSearchType;
use AcMarche\Pivot\Repository\TypeFicheRepository;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/typeoffre')]
class TypeOffreController extends AbstractController
{
    public function __construct(
        private readonly TypeOffreRepository $typeOffreRepository,
        private readonly TypeFicheRepository $typeFicheRepository,
    ) {
    }

    #[Route(path: '/', name: 'pivot_typeoffre_index')]
    public function index(Request $request): Response
    {
        $typesoffre = $this->typeOffreRepository->findWithChildren(false);
        $data = [];
        $form = $this->createForm(TypeOffreSearchType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $typesoffre = $this->typeOffreRepository->findByNameOrUrn($data['name']);

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
    public function show(TypeOffre $typeOffre): Response
    {
        $lvl1 = $this->typeOffreRepository->findByParent($typeOffre->id, false);
        foreach ($lvl1 as $lvl2) {
            $lvl2->children = $this->typeOffreRepository->findByParent($lvl2->id, false);
            foreach ($lvl2->children as $lvl3) {
                $lvl3->children = $this->typeOffreRepository->findByParent($lvl3->id, false);
                foreach ($this->typeOffreRepository->findByParent($lvl3->id, false) as $lvl4) {
                    $lvl4->children = $this->typeOffreRepository->findByParent($lvl4->id, false);
                    foreach ($this->typeOffreRepository->findByParent($lvl4->id, false) as $lvl5) {
                        $lvl5->children = $this->typeOffreRepository->findByParent($lvl5->id, false);
                        foreach ($this->typeOffreRepository->findByParent($lvl5->id, false) as $lvl6) {
                            $lvl6->children = $this->typeOffreRepository->findByParent($lvl6->id, false);
                            foreach ($this->typeOffreRepository->findByParent($lvl6->id, false) as $lvl7) {
                                $lvl7->children = $this->typeOffreRepository->findByParent($lvl7->id, false);
                                foreach ($this->typeOffreRepository->findByParent($lvl7->id, false) as $lvl8) {
                                    $lvl8->children = $this->typeOffreRepository->findByParent($lvl8->id, false);
                                }
                            }
                        }
                    }
                }
            }
        }
        $typeOffre->children = $lvl1;

        return $this->render(
            '@AcMarchePivot/typeoffre/show.html.twig',
            [
                'typeOffre' => $typeOffre,
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

    #[Route(path: '/fiche', name: 'pivot_typefiche_index')]
    public function fiche(Request $request): Response
    {
        $types = $this->typeFicheRepository->findAllOrdered();

        return $this->render('@AcMarchePivot/typeoffre/type_fiche.html.twig', [
            'types' => $types,
        ]);
    }
}
