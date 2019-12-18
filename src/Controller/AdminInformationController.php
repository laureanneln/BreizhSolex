<?php

namespace App\Controller;

use App\Entity\Hour;
use App\Entity\Info;
use App\Form\HourType;
use App\Form\PhoneType;
use App\Form\AddressType;
use App\Repository\HourRepository;
use App\Repository\InfoRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminInformationController extends AbstractController
{
    /**
     * @Route("/admin/informations", name="admininformations")
     */
    public function index(InfoRepository $info, HourRepository $hour) {
        return $this->render('admin/information/index.html.twig', [
            'hours' => $hour->findAll(),
            'infos' => $info->findAll()
        ]);
    }

    /**
     * @Route("/admin/informations/{day_name}/modifier", name="adminhour_edit")
     * 
     * @param Hour $hour
     * @return Response
     */
    public function editHour(Hour $hour, Request $request, ObjectManager $manager) {
        $form = $this->createForm(HourType::class, $hour);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($hour);
            $manager->flush();

            return $this->redirectToRoute("admininformations");
        }

        return $this->render('admin/information/editHour.html.twig', [
            'hour' => $hour,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/informations/{id}/telephone", name="adminphone_edit")
     * 
     * @param Info $info
     * @return Response
     */
    public function editPhone(Info $info, Request $request, ObjectManager $manager) {
        $form = $this->createForm(PhoneType::class, $info);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($info);
            $manager->flush();

            return $this->redirectToRoute("admininformations");
        }

        return $this->render('admin/information/editPhone.html.twig', [
            'info' => $info,
            'form' => $form->createView()    
        ]);
    }

    /**
     * @Route("/admin/informations/{id}/adresse", name="adminaddress_edit")
     * 
     * @param Info $info
     * @return Response
     */
    public function editAddress(Info $info, Request $request, ObjectManager $manager) {
        $form = $this->createForm(AddressType::class, $info);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($info);
            $manager->flush();

            return $this->redirectToRoute("admininformations");
        }

        return $this->render('admin/information/editAddress.html.twig', [
            'info' => $info,
            'form' => $form->createView()    
        ]);
    }
}
