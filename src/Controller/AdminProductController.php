<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminProductController extends AbstractController
{
    /**
     * Permet d'afficher la liste des produits
     * @Route("/admin/produits", name="adminproducts")
     */
    public function index(ProductRepository $repo) {

        $total = count($repo->findAll());

        return $this->render('admin/product/index.html.twig', [
            'products' => $repo->findAll(),
            'total' => $total
        ]);
    }

    /**
     * Permet de créer un produit
     *
     * @Route("admin/produits/creer", name="adminproduct_create")
     * 
     * @return Response
     */
    public function create(Request $request, ObjectManager $manager) {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $manager->persist($product);
            $manager->flush();

            return $this->redirectToRoute('adminproducts');
        };

        return $this->render('admin/product/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     * 
     * @Route("/admin/produits/{id}/modifier", name="adminproduct_edit")
     *
     * @param Product $product
     * @return Response
     */
    public function edit(Product $product, Request $request, ObjectManager $manager) {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($product);
            $manager->flush();

            return $this->redirectToRoute("adminproducts");
        }

        return $this->render('admin/product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un produit
     *
     * @Route("/admin/produits/{id}/supprimer", name="adminproduct_delete")
     * 
     * @param Product $product
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Product $product, ObjectManager $manager) {

        $manager->remove($product);
        $manager->flush();

        return $this->redirectToRoute('adminproducts');
    }
}
