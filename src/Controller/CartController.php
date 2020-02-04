<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\Address;
use App\Entity\CustomerOrder;
use App\Form\BillAddressType;
use App\Form\UserAddressType;
use App\Repository\CartRepository;
use App\Repository\ItemRepository;
use App\Repository\StatusRepository;
use App\Repository\AddressRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CustomerOrderRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController {
    
    /**
     * @Route("/panier", name="cartpage")
     */
    public function index(CartRepository $repo){

        $cart = '';
        $subTotal = '';

        if ($this->getUser()) {
            $cart = $repo->findOneBy(['user' => $this->getUser()]);

            $subTotal = 0;
    
            foreach ($cart->getItems() as $item) {
                $subTotal = $subTotal + $item->getProduct()->getTaxePrice() * $item->getQuantity();
            }
        }
        

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'subTotal' => $subTotal
        ]);
    }

    /**
     * Permet de supprimer un produit du panier
     *
     * @Route("/panier/{id}/supprimer", name="product_delete")
     * 
     * @param Item $item
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Item $item, EntityManagerInterface $manager) {

        $manager->remove($item);
        $manager->flush();

        $this->addFlash(
            'success',
            '<i class="fas fa-check-circle"></i> Le produit a bien été supprimé du panier.'
        );

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * Permet de modifier la quantité d'un produit du panier
     *
     * @Route("/panier/{id}/modifier", name="product_update")
     * 
     * @param Item $item
     * @param ObjectManager $manager
     * @return Response
     */
    public function update(Item $item, EntityManagerInterface $manager) {

        $quantity = $_GET['quantity'];

        if ($quantity == 0) {
            $manager->remove($item);
        } else {
            $item->setQuantity($quantity);

            $manager->persist($item);
        };

        $manager->flush();

        $this->addFlash(
            'success',
            '<i class="fas fa-check-circle"></i> La quantité a bien été modifiée.'
        );

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * Page de sélection de l'adresse de livraison
     * 
     * @Route("/adresse", name="addresspage")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function chooseAddress(CartRepository $repo, EntityManagerInterface $manager, Request $request){
        
        
        $cart = $repo->findOneBy(['user' => $this->getUser()]);

        if (count($cart->getItems()) == 0) {
            return $this->redirectToRoute("cartpage");
        }
        $totalItems = 0;
        $subTotal = 0;

        foreach ($cart->getItems() as $item) {
            $totalItems = $totalItems + $item->getQuantity();
        }
    
        foreach ($cart->getItems() as $item) {
            $subTotal = $subTotal + $item->getProduct()->getTaxePrice() * $item->getQuantity();
        }

        $address = new Address();

        $formAdd = $this->createForm(UserAddressType::class, $address);

        $formAdd->handleRequest($request);

        if ($formAdd->isSubmitted() && $formAdd->isValid()) {
            $address->setUser($this->getUser());
            $manager->persist($address);
            $manager->flush();

            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> L\'adresse a bien été ajoutée.'
            );

            return $this->redirectToRoute("addresspage");
        }

        $user = $this->getUser();

        $formUp = $this->createForm(BillAddressType::class, $user);

        $formUp->handleRequest($request);

        if ($formUp->isSubmitted() && $formUp->isValid()) {
            $address->setUser($this->getUser());
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> L\'adresse de facturation a bien été modifiée.'
            );

            return $this->redirectToRoute("addresspage");
        }

        return $this->render('cart/address.html.twig', [
            'cart' => $cart,
            'totalItems' => $totalItems,
            'subTotal' => $subTotal,
            'formAdd' => $formAdd->createView(),
            'formUp' => $formUp->createView()
        ]);
    }

    /**
     * @Route("/processing", name="processpage")
     * @IsGranted("ROLE_USER")
     */
    public function process(ItemRepository $item, \Swift_Mailer $mailer, CartRepository $repo, StatusRepository $sta, EntityManagerInterface $manager, CustomerOrderRepository $or, ProductRepository $pro, AddressRepository $ad){

        $cart = $repo->findOneBy(['user' => $this->getUser()]);

        $address_id = $_GET['address'];
        $address = $ad->findOneBy(['id' => $address_id]);
        $addressName = $address->getPostName() . ' – ' . $address->getAddress() . ' ' . $address->getAddress2() . ', ' . $address->getZipCode() . ' ' . $address->getCity();


        $order = new CustomerOrder();
        $subTotal = 0;

        foreach ($cart->getItems() as $item) {
            $subTotal = $subTotal + $item->getProduct()->getTaxePrice() * $item->getQuantity();
        }
        
        $status = $sta->findOneBy(['id' => 1]);

        $order->setUser($this->getUser())
                ->setStatus($status)
                ->setTotalPrice($subTotal)
                ->setDeliveryPrice(3)
                ->setOrderDate(new \DateTime('now'))
                ->setAddress($addressName);
        
        $manager->persist($order);
        $manager->flush();

        $customerOrder = $or->findOneBy(['id' => $order->getId()]);
        
        foreach ($cart->getItems() as $item) {
            $item->setCustomerOrder($customerOrder);
            $item->setCart(null);

            $product = $pro->findOneBy(['id' => $item->getProduct()->getId()]);
            $product->setQuantity($product->getQuantity() - $item->getQuantity());
        };

        $manager->flush();

        $message = (new \Swift_Message('Récapitulatif de votre commande #' . $order->getId()))
        ->setFrom('laure-anne@leneel.fr')
        ->setTo('laure-anne@leneel.fr')
        ->setBody(
            $this->renderView(
                'emails/orderSuccess.html.twig', [
                    'order' => $order,
                    'user' => $this->getUser()
                ]
            ),
            'text/html'
        );

        $mailer->send($message);

        $message = (new \Swift_Message('Nouvelle commande'))
        ->setFrom('laure-anne@leneel.fr')
        ->setTo('laure-anne@leneel.fr')
        ->setBody(
            $this->renderView(
                'emails/orderNew.html.twig', [
                    'order' => $order
                ]
            ),
            'text/html'
        );

        $mailer->send($message);
        
        return $this->redirectToRoute("validationpage");
    }

    /**
     * @Route("/validation", name="validationpage")
     * @IsGranted("ROLE_USER")
     */
    public function validation(CustomerOrderRepository $repo){

        $order = $repo->findOneBy([], ['id' => 'desc']);
        

        return $this->render('cart/validation.html.twig', [
            'order' => $order->getId()
        ]);
    }
}
