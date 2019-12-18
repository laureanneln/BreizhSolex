<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CustomerOrderRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController {
    /**
     * Permet d'afficher la liste des clients
     * @Route("/admin/clients", name="adminusers")
     */
    public function index(UserRepository $repo) {

        $total = count($repo->findAll()) - 1;

        return $this->render('admin/user/index.html.twig', [
                'users' => $repo->findAll(),
                'total' => $total
            ]);
    }

    /**
     * Permet d'afficher les détails d'un client
     * @Route("/admin/clients/{id}", name="adminuser")
     * 
     * @return Reponse
     */
    public function show(User $user, CustomerOrderRepository $repo ) {

        return $this->render('admin/user/show.html.twig', [
                'user' => $user
            ]);
    }
}
