<?php

namespace App\Controller;

use App\Entity\Subcategory;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SubcategoryController extends AbstractController {
    /**
     * @Route("/boutique/{slug}", name="subcategorypage")
     * 
     * @return Response
     */
    public function index(Subcategory $subcategory, CategoryRepository $cat, ProductRepository $pro) {

        $categories = $cat->findAll();
        $products = $pro->findBy(array('subcategory' => $subcategory->getId()));

        return $this->render('subcategory/index.html.twig', [
            'subcategory' => $subcategory,
            'categories' => $categories,
            'products' => $products
        ]);
    }
}
