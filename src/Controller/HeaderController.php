<?php

namespace App\Controller;

use App\Repository\CartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HeaderController extends AbstractController {

    public function index(CartRepository $repo) {
        $totalItems = 0;

        if ($this->getUser()) {
            $cart = $repo->findOneBy(['user' => $this->getUser()]);

            foreach ($cart->getItems() as $item) {
                $totalItems = $totalItems + $item->getQuantity();
            }
    }   

        return $this->render('partials/cartButton.html.twig', [
            'totalItems' => $totalItems,
        ]);
    }
}
