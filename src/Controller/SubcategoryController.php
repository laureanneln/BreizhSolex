<?php

namespace App\Controller;

use App\Entity\Subcategory;
use App\Repository\CartRepository;
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
    public function index(Subcategory $subcategory, CategoryRepository $cat, ProductRepository $pro, CartRepository $repo) {

        $categories = $cat->findAll();
        $products = $pro->findBy(array('subcategory' => $subcategory->getId()));

        $outOfStock = [];

        if ($this->getUser()) {

            foreach ($products as $product) {
                if ($product->getQuantity() == 0) {
                    array_push($outOfStock, $product->getId());
                }
                $leftQuantity = $product->getQuantity();
    
                $cart = $repo->findOneBy(['user' => $this->getUser()]);
                
                foreach($cart->getItems() as $item) {
                    if ($item->getProduct()->getId() == $product->getId()) {
                        
                        $leftQuantity = $leftQuantity - $item->getQuantity();
    
                        if ($leftQuantity == 0) {
                            array_push($outOfStock, $product->getId());
                        }
                    }
                }
    
            }
        }

        return $this->render('subcategory/index.html.twig', [
            'subcategory' => $subcategory,
            'categories' => $categories,
            'products' => $products,
            'outOfStock' => $outOfStock
        ]);
    }
}
