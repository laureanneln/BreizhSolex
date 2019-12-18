<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {
    /**
     * @Route("/", name="homepage")
     */
    public function index(ProductRepository $repo){
        $products = $repo->findAll();

        return $this->render('home.html.twig', [
            'products' => $repo->findBy([], [], 12, 0)
        ]);
    }
}
