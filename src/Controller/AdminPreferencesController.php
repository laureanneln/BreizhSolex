<?php

namespace App\Controller;

use App\Entity\Preference;
use App\Form\MinstockType;
use App\Form\TaxeType;
use App\Repository\PreferenceRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminPreferencesController extends AbstractController {
    /**
     * @Route("/admin/preferences", name="adminpreferences")
     */
    public function index(PreferenceRepository $pref)
    {
        return $this->render('admin/preferences/index.html.twig', [
            'pref' => $pref->findOneBy(array('id' => 1))
        ]);
    }

    /**
     * Permet de modifier le stock minimum
     * @Route("/admin/preferences/{id}/stock-minimum", name="adminpreferences_minstock")
     * 
     * @param Preference $preference
     * @return Response
     */
    public function minstock(Preference $preference, Request $request, EntityManagerInterface $manager){
        $form = $this->createForm(MinstockType::class, $preference);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($preference);
            $manager->flush();
            
            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> Le stock minimum a bien été modifié.'
            );

            return $this->redirectToRoute("adminpreferences");
        }
        return $this->render('admin/preferences/editMinstock.html.twig', [
            'preference' => $preference,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier la TVA
     * @Route("/admin/preferences/{id}/tva", name="adminpreferences_taxe")
     * 
     * @param Preference $preference
     * @return Response
     */
    public function taxe(Preference $preference, Request $request, EntityManagerInterface $manager){
        $form = $this->createForm(TaxeType::class, $preference);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($preference);
            $manager->flush();
            
            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> La TVA a bien été modifiée.'
            );

            return $this->redirectToRoute("adminpreferences");
        }
        return $this->render('admin/preferences/editTaxe.html.twig', [
            'preference' => $preference,
            'form' => $form->createView()
        ]);
    }
}
