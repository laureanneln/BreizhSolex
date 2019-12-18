<?php

namespace App\Controller;

use App\Entity\CustomerOrder;
use App\Form\OrderType;
use App\Repository\ItemRepository;
use App\Repository\CustomerOrderRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminOrderController extends AbstractController {

    /**
     * Permet d'afficher la liste des commandes
     * @Route("/admin/commandes", name="adminorders")
     */
    public function index(CustomerOrderRepository $repo) {

        $total = count($repo->findAll());

        return $this->render('admin/order/index.html.twig', [
                'orders' => $repo->findAll(),
                'total' => $total
            ]);
    }

    /**
     * Permet d'afficher les détails d'une commande
     * @Route("/admin/commande/{id}", name="adminorder")
     * 
     * @return Reponse
     */
    public function show(CustomerOrder $order, ItemRepository $repo) {

        return $this->render('admin/order/show.html.twig', [
                'order' => $order
            ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     * 
     * @Route("/admin/commande/{id}/modifier", name="adminorder_edit")
     *
     * @param CustomerOrder $order
     * @return Response
     */
    public function edit(CustomerOrder $order, Request $request, ObjectManager $manager) {
        $form = $this->createForm(OrderType::class, $order);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($order);
            $manager->flush();

            return $this->redirectToRoute("adminorders");
        }

        return $this->render('admin/order/edit.html.twig', [
            'order' => $order,
            'form' => $form->createView()
        ]);
        }
}