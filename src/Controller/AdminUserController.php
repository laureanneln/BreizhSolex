<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEmailType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CustomerOrderRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * Permet d'afficher les détails d'un client
     * @Route("/admin/clients/{id}/modifier", name="adminuser_edit")
     * 
     * @param User $user
     * @return Response
     */
    public function edit(User $user, Request $request, EntityManagerInterface $manager) {

        $form = $this->createForm(UserEmailType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

                $manager->persist($user);
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    '<i class="fas fa-check-circle"></i> L\'adresse email a bien été modifié.'
                );

            return $this->redirectToRoute("adminuser", array('id' => $user->getId()));
        }

        return $this->render('admin/user/edit.html.twig', [
                'user' => $user,
                'form' => $form->createView()
            ]);
    }
}
