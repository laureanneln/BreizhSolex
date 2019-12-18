<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController {
    /**
     * @Route("/admin/login", name="adminlogin")
     */
    public function login(AuthenticationUtils $utils) {
            $error = $utils->getLastAuthenticationError();
            $username = $utils->getLastUsername();
            
            return $this->render('admin/account/login.html.twig', [
                'hasError' => $error !== null,
                'username' => $username
            ]);
    }

    /** Permet de se déconnecter
     * 
     * @Route("/admin/deconnexion", name="adminlogout")
     * 
     * @return void
     */
    public function logout() {
        // ...
    }
}