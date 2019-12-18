<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\StockType;
use App\Repository\PreferenceRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminStockController extends AbstractController
{
    /**
    * Permet d'afficher la liste des produits
    * @Route("/admin/stock", name="adminstock")
    *
    * @return Response
    */
    public function index(ProductRepository $repo, PreferenceRepository $pref) {
        $total = count($repo->findAll());

        return $this->render('admin/stock/index.html.twig', [
            'products' => $repo->findAll(),
            'total' => $total,
            'pref' => $pref->findOneBy(array('id' => 1))
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     * 
     * @Route("/admin/stock/{id}/modifier", name="adminstock_edit")
     *
     * @param Product $product
     * @return Response
     */
    public function edit(Product $product, Request $request, ObjectManager $manager) {
        $form = $this->createForm(StockType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($product);
            $manager->flush();
            
            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> La quantité a bien été modifiée.'
            );

            return $this->redirectToRoute("adminstock");
        }

        return $this->render('admin/stock/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }
}
