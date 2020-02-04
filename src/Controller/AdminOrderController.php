<?php

namespace App\Controller;

use App\Form\OrderType;
use App\Entity\CustomerOrder;
use App\Repository\ItemRepository;
use App\Repository\StatusRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
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

        /**
     * @Route("/admin/commande/{id}/cancel", name="adminorder_cancel")
     * 
     * 
     * @return Response
     */
    public function process(Request $request, ItemRepository $item, \Swift_Mailer $mailer, StatusRepository $sta, EntityManagerInterface $manager, CustomerOrder $order, ProductRepository $pro){
        
        $status = $sta->findOneBy(['id' => 5]);

        $order->setStatus($status);
        
        $manager->persist($order);
        $manager->flush();
        
        foreach ($order->getItems() as $item) {
            $product = $pro->findOneBy(['id' => $item->getProduct()->getId()]);
            $product->setQuantity($product->getQuantity() + $item->getQuantity());
        };

        $manager->flush();

        $message = (new \Swift_Message('Confirmation de l\'annulation de la commande #' . $order->getId()))
        ->setFrom('laure-anne@leneel.fr')
        ->setTo('laure-anne@leneel.fr')
        ->setBody(
            $this->renderView(
                'emails/cancelSuccess.html.twig', [
                    'order' => $order,
                    'user' => $order->getUser()
                ]
            ),
            'text/html'
        );

        $mailer->send($message);

        $this->addFlash(
            'success',
            '<i class="fas fa-check-circle"></i> La commande a bien été annulée.'
        );
        
        return $this->redirectToRoute("adminorders");
    }

}