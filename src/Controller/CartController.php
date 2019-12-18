<?php

namespace App\Controller;

use DateTime;
use App\Entity\Item;
use App\Entity\Address;
use App\Entity\CustomerOrder;
use App\Form\UserAddressType;
use App\Repository\AddressRepository;
use App\Repository\CartRepository;
use App\Repository\StatusRepository;
use App\Repository\ProductRepository;
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
    public function delete(Item $item, ObjectManager $manager) {

        $manager->remove($item);
        $manager->flush();

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
    public function update(Item $item, ObjectManager $manager) {

        $quantity = $_GET['quantity'];

        if ($quantity == 0) {
            $manager->remove($item);
        } else {
            $item->setQuantity($quantity);

            $manager->persist($item);
        };

        $manager->flush();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * Page de sélection de l'adresse de livraison
     * 
     * @Route("/adresse", name="addresspage")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function chooseAddress(CartRepository $repo, ObjectManager $manager, Request $request){
        
        $cart = $repo->findOneBy(['user' => $this->getUser()]);
        $totalItems = 0;
        $subTotal = 0;

        foreach ($cart->getItems() as $item) {
            $totalItems = $totalItems + $item->getQuantity();
        }
    
        foreach ($cart->getItems() as $item) {
            $subTotal = $subTotal + $item->getProduct()->getTaxePrice() * $item->getQuantity();
        }

        $address = new Address();

        $form = $this->createForm(UserAddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $manager->persist($address);
            $manager->flush();

            return $this->redirectToRoute("addresspage");
        }

        return $this->render('cart/address.html.twig', [
            'cart' => $cart,
            'totalItems' => $totalItems,
            'subTotal' => $subTotal,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/processing", name="processpage")
     * @IsGranted("ROLE_USER")
     */
    public function process(CartRepository $repo, StatusRepository $sta, ObjectManager $manager, CustomerOrderRepository $or, ProductRepository $pro, AddressRepository $ad){

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
