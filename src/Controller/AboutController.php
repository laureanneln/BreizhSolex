<?php

namespace App\Controller;

use App\Repository\AboutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController {
    /**
     * @Route("/a-propos", name="aboutpage")
     */
    public function index(AboutRepository $repo) {
        $abouts = $repo->findAll();

        return $this->render('about/index.html.twig', [
            'abouts' => $abouts
        ]);
    }
}
