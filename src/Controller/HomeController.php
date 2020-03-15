<?php

namespace App\Controller;

use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {
    /**
     * @Route("/", name="homepage")
     */
    public function index(ProductRepository $repo, CartRepository $ca){
        $products = $repo->findAll();

        $outOfStock = [];

        if ($this->getUser()) {
            foreach ($products as $product) {
                if ($product->getQuantity() == 0) {
                    array_push($outOfStock, $product->getId());
                }
                $leftQuantity = $product->getQuantity();
    
                $cart = $ca->findOneBy(['user' => $this->getUser()]);
                
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

        return $this->render('home.html.twig', [
            'products' => $repo->findAllLimited(),
            'outOfStock' => $outOfStock
        ]);
    }
}
