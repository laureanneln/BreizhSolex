<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\User;
use App\Form\InvoiceType;
use App\Entity\CustomerOrder;
use App\Repository\UserRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminInvoiceController extends AbstractController {

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }
    
    /**
     * @Route("/admin/facturation", name="admininvoice")
     * 
     * @return Response
     */
    public function index(EntityManagerInterface $manager, Request $request, UserRepository $user, StatusRepository $sta) {
        $order = new CustomerOrder();

        $form = $this->createForm(InvoiceType::class);

        $form->handleRequest($request);
        $items = new ArrayCollection();

        foreach ($order->getItems() as $item) {
            $items->add($item);
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $subTotal = 0;
            foreach ($items as $item) {
                if (false === $order->getItems()->contains($item)) {
                    // remove the Task from the Tag
                    $item->getCustomerOrders()->removeElement($order);
    
                    // if it was a many-to-one relationship, remove the relationship like this
                    // $tag->setTask(null);
    
                    $manager->persist($item);
    
                    // if you wanted to delete the Tag entirely, you can also do that
                    // $entityManager->remove($tag);
                }
            }
            // foreach ($order->getItems() as $item){
            //     $item->setCustomerOrder($order);
            //     $manager->persist($item);

            //     $subTotal = $subTotal + $item->getProduct()->getTaxePrice() * $item->getQuantity();
            // }

            $email = $form['email']->getData();

            if ($user->findOneByEmail($email)) {
                $newUser = $user->findOneByEmail($email);
            } else {
                $newUser = new User();

                $date = new \DateTime('now');
                $hash = $this->encoder->encodePassword($newUser, 'BS' . $date->format('D-M-Y'));

                $newUser->setFirstName($form['firstName']->getData())
                        ->setLastName($form['lastName']->getData())
                        ->setEmail($form['email']->getData())
                        ->setPhoneNumber($form['phone']->getData())
                        ->setRegisterDate($date)
                        ->setPassword($hash);

                $manager->persist($newUser);

                $cart = new Cart();
                $cart->setUser($newUser);

                $manager->persist($cart);

                $manager->flush();
            }

            $status = $sta->findOneByLabel('Terminée');

            $order->setTotalPrice($subTotal)
                    ->setDeliveryPrice(0)
                    ->setOrderDate(new \DateTime('now'))
                    ->setAddress('BreizhSolex')
                    ->setStatus($status)
                    ->setUser($newUser);

            $manager->persist($order);
            $manager->flush();

            return $this->redirectToRoute("admininvoice");
        }

        return $this->render('admin/invoice/index.html.twig', [
           'form' => $form->createView()
        ]);
    }
}
