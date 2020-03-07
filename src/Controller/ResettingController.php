<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\Mailer;
use App\Form\ResettingType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ResettingController extends AbstractController
{
    /**
     * @Route("/mot-de-pass-oublie", name="passwordreset")
     */
    public function request(Request $request, UserRepository $repo, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        // création d'un formulaire "à la volée", afin que l'internaute puisse renseigner son mail
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email(),
                    new NotBlank()
                ]
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $user = $repo->loadUserByUsername($form->getData()['email']);

            // aucun email associé à ce compte.
            if (!$user) {
                $this->addFlash(
                    'danger',
                    '<i class="fas fa-times-circle"></i> L\'adresse mail n\'existe pas.'
                );
                return $this->redirectToRoute("passwordreset");
            } 

            // création du token
            $user->setToken($tokenGenerator->generateToken());
            // enregistrement de la date de création du token
            $user->setPasswordRequestedAt(new \Datetime());
            $em->flush();

            $message = (new \Swift_Message('Réinitialisation de votre mot de passe'))
            ->setFrom('laure-anne@leneel.fr')
            ->setTo('laure-anne@leneel.fr')
            // ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/resetPassword.html.twig', [
                        'user' => $user
                    ]
            ),
            'text/html'
        );

        $mailer->send($message);
            $this->addFlash(
                'success',
                'Un mail va vous être envoyé afin que vous puissiez renouveller votre mot de passe. Le lien que vous recevrez sera valide 10 minutes.'
            );
            

            return $this->redirectToRoute("loginpage");
        }

        return $this->render('account/resetting.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // si supérieur à 10min, retourne false
    // sinon retourne false
    private function isRequestInTime(\Datetime $passwordRequestedAt = null)
    {
        if ($passwordRequestedAt === null)
        {
            return false;        
        }
        
        $now = new \DateTime();
        $interval = $now->getTimestamp() - $passwordRequestedAt->getTimestamp();

        $daySeconds = 60 * 10;
        $response = $interval > $daySeconds ? false : $reponse = true;
        return $response;
    }

    /**
     * @Route("/mot-de-pass-oublie/{id}/{token}", name="resetting")
     */
    public function resetting(User $user, $token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // interdit l'accès à la page si:
        // le token associé au membre est null
        // le token enregistré en base et le token présent dans l'url ne sont pas égaux
        // le token date de plus de 10 minutes
        if ($user->getToken() === null || $token !== $user->getToken() || !$this->isRequestInTime($user->getPasswordRequestedAt()))
        {
            $this->addFlash(
                'danger',
                'Le lien que vous avez utilisé n\'est plus valide'
            );
            
            return $this->redirectToRoute("loginpage");
        }

        $form = $this->createForm(ResettingType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // réinitialisation du token à null pour qu'il ne soit plus réutilisable
            $user->setToken(null);
            $user->setPasswordRequestedAt(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                'Votre mot de passe a été renouvelé.'
            );

            return $this->redirectToRoute('loginpage');

        }

        return $this->render('account/resettingPassword.html.twig', [
            'form' => $form->createView()
        ]);
        
    }

}