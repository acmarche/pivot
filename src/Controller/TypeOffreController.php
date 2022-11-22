<?php

namespace AcMarche\Pivot\Controller;

use AcMarche\Pivot\Entity\TypeOffre;
use AcMarche\Pivot\Form\TypeOffreEditType;
use AcMarche\Pivot\Form\TypeOffreSearchType;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use AcMarche\Pivot\Utils\SortUtils;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/typeoffre')]
#[IsGranted(data: 'ROLE_PIVOT')]
class TypeOffreController extends AbstractController
{
    public function __construct(
        private TypeOffreRepository $typeOffreRepository,
        private PivotRepository $pivotRepository,
    ) {
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
    public function show(TypeOffre $typeOffre): Response
    {
        $lvl1 = $this->typeOffreRepository->findByParent($typeOffre->id);
        foreach ($lvl1 as $lvl2) {
            $lvl2->children = $this->typeOffreRepository->findByParent($lvl2->id);
            foreach ($lvl2->children as $lvl3) {
                $lvl3->children = $this->typeOffreRepository->findByParent($lvl3->id);
                foreach ($this->typeOffreRepository->findByParent($lvl3->id) as $lvl4) {
                    $lvl4->children = $this->typeOffreRepository->findByParent($lvl4->id);
                    foreach ($this->typeOffreRepository->findByParent($lvl4->id) as $lvl5) {
                        $lvl5->children = $this->typeOffreRepository->findByParent($lvl5->id);
                        foreach ($this->typeOffreRepository->findByParent($lvl5->id) as $lvl6) {
                            $lvl6->children = $this->typeOffreRepository->findByParent($lvl6->id);
                            foreach ($this->typeOffreRepository->findByParent($lvl6->id) as $lvl7) {
                                $lvl7->children = $this->typeOffreRepository->findByParent($lvl7->id);
                                foreach ($this->typeOffreRepository->findByParent($lvl7->id) as $lvl8) {
                                    $lvl8->children = $this->typeOffreRepository->findByParent($lvl8->id);
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
}
