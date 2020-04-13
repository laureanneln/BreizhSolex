<?php

namespace App\Controller;

use App\Entity\About;
use App\Form\AboutType;
use App\Repository\AboutRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAboutController extends AbstractController
{
    /**
     * @Route("/admin/a-propos", name="adminabout")
     */
    public function index(AboutRepository $about)
    {
        return $this->render('admin/about/index.html.twig', [
            'abouts' => $about->findAll()
        ]);
    }

    /**
     * Permet de créer un produit
     *
     * @Route("admin/a-propos/creer", name="adminabout_create")
     * 
     * @return Response
     */
    public function create(Request $request, ObjectManager $manager) {
        $about = new About();

        $form = $this->createForm(AboutType::class, $about);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $manager->persist($about);
            $manager->flush();

            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> La section a bien été ajoutée.'
            );

            return $this->redirectToRoute('adminabout');
        };

        return $this->render('admin/about/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/a-propos/{id}/edit", name="adminabout_edit")
     * 
     * @param About $about
     * @return Response
     */
    public function editPhone(About $about, Request $request, ObjectManager $manager) {
        $form = $this->createForm(AboutType::class, $about);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($about);
            $manager->flush();

            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> La section a bien été modifiée.'
            );

            return $this->redirectToRoute("adminabout");
        }

        return $this->render('admin/about/edit.html.twig', [
            'about' => $about,
            'form' => $form->createView()    
        ]);
    }

    /**
     * Permet de supprimer un onglet
     *
     * @Route("/admin/a-propos/{id}/supprimer", name="adminabout_delete")
     * 
     * @param About $about
     * @return Response
     */
    public function delete(About $about) {
        
        return $this->render('admin/about/delete.html.twig', [
            'about' => $about
        ]);
    }

    /**
     * Permet de supprimer un onglet
     *
     * @Route("/admin/a-propos/{id}/delete", name="adminabout_suretodelete")
     * 
     * @param About $product
     * @param ObjectManager $manager
     * @return Response
     */
    public function suretodelete(About $about, ObjectManager $manager) {

        $manager->remove($about);
        $manager->flush();

        $this->addFlash(
            'success',
            '<i class="fas fa-check-circle"></i> La section a bien été supprimée.'
        );

        return $this->redirectToRoute('adminabout');
    }
}
