<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Form\BrandType;
use App\Repository\BrandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BrandController extends AbstractController
{
    #[Route('/brands', name: 'app_brand')]
    public function index(BrandRepository $brandRepository): Response
    {
        $brands = $brandRepository->findAll();
        
        return $this->render('brand/index.html.twig', ['brands' => $brands]);
    }

    /**
     * Activate or deactivate Api For Brand
     */
    #[Route('/brand/{id}/active', name: 'app_active_brand')]
    public function active(BrandRepository $brandRepository, int $id): JsonResponse
    {
        $brand = $brandRepository->find($id);

        if (!$brand) {            
            return $this->redirectToRoute('app_brand');
        } else {
            $brand->setActive($brand->isActive() ? false : true);
            $brandRepository->save($brand, true);
        }

        return $this->json(
            $brand,
            200,
            [],
            ['groups' => 'brand']
        );
    }
    /**
     * Edit A Brand
     */
    #[Route('/brand/{id}/edit', name: 'app_edit_brand')]
    public function edit(
        Request $request, 
        EntityManagerInterface $entityManager, 
        Brand $brand
    ): Response {
        $originalModels = new ArrayCollection();

        foreach ($brand->getModels() as $model) {
            $originalModels->add($model);
        }

        $form = $this->createForm(BrandType::class, $brand);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($originalModels as $model) {
                if (false === $brand->getModels()->contains($model)) {
                    $brand->removeModel($model);
                    $entityManager->persist($model);
                }
            }

            $entityManager->persist($brand);
            $entityManager->flush();

            $this->addFlash('success', 'Modifications enregistrées');

            return $this->redirectToRoute('app_brand');
        }

        return $this->render('brand/edit.html.twig', [
            'brand' => $brand,
            'form' => $form->createView(),
        ]);
    }
}
