<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\HourRepository;
use App\Repository\InfoRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contactpage")
     */
    public function index(InfoRepository $infos, HourRepository $hours, \Swift_Mailer $mailer) {

        $form = $this->createForm(ContactType::class);

        $informations = $infos->findBy(array('id' => 1));
        $hours = $hours->findAll();

        $today = date('now');

        if ($form->isSubmitted() && $form->isValid()) {
            $fistName = $form["firstName"]->getData();
            $lastName = $form["lastName"]->getData();
            $phone = $form["phone"]->getData();
            $email = $form["email"]->getData();
            $content = $form["message"]->getData();

            $message = (new \Swift_Message('BreizhSolex – Nouvelle prise de contact'))
            ->setFrom('laure-anne@leneel.fr')
            ->setTo('laure-anne@leneel.fr')
            ->setBody(
                $this->renderView(
                    'emails/newMessage.html.twig', [
                        'firstName' => $fistName,
                        'lastName' => $lastName,
                        'phone' => $phone,
                        'email' => $email,
                        'message' => $content
                    ]
                ),
                'text/html'
            );

            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> Votre message a bien été envoyé.'
            );

            $mailer->send($message);
            
            return $this->redirectToRoute("contactpage");
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
            'informations' => $informations,
            'hours' => $hours
        ]);
    }
}
