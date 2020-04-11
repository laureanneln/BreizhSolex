<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\User;
use App\Form\UserType;
use App\Entity\Address;
use App\Entity\CustomerOrder;
use App\Form\BillAddressType;
use App\Form\UserAddressType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use App\Repository\ItemRepository;
use App\Repository\StatusRepository;
use App\Repository\ProductRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CustomerOrderRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController {
    /**
     * Permet d'afficher et de gérer le formulaire de connexion
     * 
     * @Route("/connexion", name="loginpage")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils) {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        
        
        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * Permet de se déconnecter
     * 
     * @Route("/deconnexion", name="logoutpage")
     * 
     * @return void
     */
    public function logout() {
        //
    }

    /**
     * Permet d'afficher le formulaire d'inscription
     * 
     * @Route("/inscription", name="registrationpage")
     *
     * @return Response
     */
    public function register(\Swift_Mailer $mailer, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);

            $cart = new Cart();
            $cart->setUser($user);
            $manager->persist($cart);

            $manager->flush();

            $message = (new \Swift_Message('Bienvenue sur BreizhSolex !'))
            ->setFrom('laure-anne@leneel.fr')
            ->setTo('laure-anne@leneel.fr')
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig', [
                        'user' => $user
                    ]
                ),
                'text/html'
            );

            $mailer->send($message);

            $this->addFlash(
                'success',
                'Votre compte a bien été créé ! Vous pouvez maintenant vous connecter.'
            );

            return $this->redirectToRoute("loginpage");
        } else {
            $errors = $form->getErrors();
            $this->addFlash('danger', 'Il y a des erreurs dans votre formulaire.');
        }
 
        return $this->render('account/registration.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/compte", name="accountpage")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function index(CustomerOrderRepository $order, UserInterface $user) {

        $orders = $order->findByUser($user);
        
        return $this->render('account/index.html.twig', [
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/compte/commandes", name="accountpage_orders")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function orders(CustomerOrderRepository $or) {

        $orders = $or->findAllByUser($this->getUser()->getId());
        
        return $this->render('account/orders.html.twig', [
            'orders' => $orders
        ]);
    }

    /**
     * Affichage d'une commande
     * 
     * @Route("/compte/commandes/{id}", name="accountpage_order")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function order(CustomerOrder $order, CustomerOrderRepository $repo) {

        $subTotal = 0;
    
        foreach ($order->getItems() as $item) {
            $subTotal = $subTotal + $item->getProduct()->getTaxePrice() * $item->getQuantity();
        }

        return $this->render('account/order.html.twig', [
            'order' => $order,
            'subTotal' => $subTotal
        ]);
    }

    /**
     * @Route("/compte/informations/{id}", name="accountpage_editinfos")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function editInfos(User $user, Request $request, EntityManagerInterface $manager) {

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> Les informations ont bien été enregistrées.'
            );

            return $this->redirectToRoute("accountpage");
        }
        
        return $this->render('account/editInfos.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/commandes/{id}/cancel", name="accountpage_cancel")
     * @IsGranted("ROLE_USER")
     * 
     * 
     * @return Response
     */
    public function process(Request $request, ItemRepository $item, \Swift_Mailer $mailer, StatusRepository $sta, EntityManagerInterface $manager, CustomerOrder $order, ProductRepository $pro){
        
        $status = $sta->findOneBy(['id' => 4]);

        $order->setStatus($status);
        
        $manager->persist($order);
        $manager->flush();
        
        foreach ($order->getItems() as $item) {
            $product = $pro->findOneBy(['id' => $item->getProduct()->getId()]);
            $product->setQuantity($product->getQuantity() + $item->getQuantity());
        };

        $manager->flush();

        $message = (new \Swift_Message('Confirmationde l\'annulation de la commande #' . $order->getId()))
        ->setFrom('laure-anne@leneel.fr')
        ->setTo('laure-anne@leneel.fr')
        ->setBody(
            $this->renderView(
                'emails/cancelSuccess.html.twig', [
                    'order' => $order,
                    'user' => $this->getUser()
                ]
            ),
            'text/html'
        );

        $mailer->send($message);

        $message = (new \Swift_Message('Annulation de la commande #' . $order->getId()))
        ->setFrom('laure-anne@leneel.fr')
        ->setTo('laure-anne@leneel.fr')
        ->setBody(
            $this->renderView(
                'emails/cancelOrder.html.twig', [
                    'order' => $order,
                    'user' => $this->getUser()
                ]
            ),
            'text/html'
        );

        $mailer->send($message);

        $this->addFlash(
            'success',
            '<i class="fas fa-check-circle"></i> La commande a bien été annulée.'
        );
        
        return $this->redirectToRoute("accountpage_orders");
    }

    /**
     * @Route("/compte/adresse/ajouter", name="accountpage_addaddress")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function addAddress(Request $request, EntityManagerInterface $manager) {

        $address = new Address();

        $form = $this->createForm(UserAddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $manager->persist($address);
            $manager->flush();

            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> L\'adresse a bien été ajoutée.'
            );

            return $this->redirectToRoute("accountpage");
        }
        
        return $this->render('account/addAddress.html.twig', [
            'address' => $address,
            'form' => $form->createView()
        ]);
    }
/**
     * @Route("/compte/adresse/{id}/ajouter", name="accountpage_addbilladdress")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function addBillAddress(Request $request, EntityManagerInterface $manager, User $user) {

        $form = $this->createForm(BillAddressType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> L\'adresse de facturation a bien été ajoutée.'
            );

            return $this->redirectToRoute("accountpage");
        }
        
        return $this->render('account/addBillAddress.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/adresse/{id}", name="accountpage_editaddress")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function editAddress(Address $address, Request $request, EntityManagerInterface $manager) {

        $form = $this->createForm(UserAddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($address);
            $manager->flush();

            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> L\'adresse a bien été modifiée.'
            );

            return $this->redirectToRoute("accountpage");
        }
        
        return $this->render('account/editAddress.html.twig', [
            'address' => $address,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/compte/adresse/{id}/modifier", name="accountpage_editbilladdress")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function editBillAddress(User $user, Request $request, EntityManagerInterface $manager) {

        $form = $this->createForm(BillAddressType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> L\'adresse de facturation a bien été modifiée.'
            );

            return $this->redirectToRoute("accountpage");
        }
        
        return $this->render('account/editBillAddress.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/adresse/{id}/delete", name="accountpage_deleteaddress")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function deleteAddress(Address $address, Request $request, EntityManagerInterface $manager) {

            $manager->remove($address);
            $manager->flush();

            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> L\'adresse a bien été supprimée.'
            );

            return $this->redirectToRoute("accountpage");
    }

    /**
     * Permet de modifier le mot de passe
     * 
     * @Route("/compte/mot-de-passe/{id}", name="accountpage_editpassword")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function editPassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager) {
        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 1. Vérifier que le oldPassword du formulaire soit le même que le password du formulaire
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getPassword())) {
                $form->get('old_password')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setPassword($hash);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    '<i class="fas fa-check-circle"></i> Le mot de passe a bien été modifiée.'
                );

                return $this->redirectToRoute('accountpage');
            }
        }

        return $this->render('account/editPassword.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
