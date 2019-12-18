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
    public function index(InfoRepository $infos, HourRepository $hours) {

        $form = $this->createForm(ContactType::class);

        $informations = $infos->findBy(array('id' => 1));
        $hours = $hours->findAll();

        $today = date('now');

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
            'informations' => $informations,
            'hours' => $hours
        ]);
    }
}
