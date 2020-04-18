<?php

namespace App\Controller;

use DateTime;
use App\Entity\Product;
use App\Form\ProductType;
use Cocur\Slugify\Slugify;
use App\Repository\ProductRepository;
use App\Repository\PreferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AdminProductController extends AbstractController {
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }
    
    /**
     * Permet d'afficher la liste des produits
     * @Route("/admin/produits", name="adminproducts")
     */
    public function index(ProductRepository $repo) {

        $total = count($repo->findAll());

        return $this->render('admin/product/index.html.twig', [
            'products' => $repo->findAllProducts(),
            'total' => $total
        ]);
    }

    /**
     * Permet de créer un produit
     *
     * @Route("admin/produits/creer", name="adminproduct_create")
     * 
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager, PreferenceRepository $pref) {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $file = $form['imageFile']->getData();
            if ($file) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $directory = $this->params->get('products_directory');
                $file->move($directory, $fileName);

                $product->setImage($fileName);
            }
           


            $tva = $pref->findOneBy(['id' => 1])->getTaxe();

            $price = $form['taxePrice']->getData();
            $noTaxePrice = $price - (($price / 120) * $tva);
            $name = $form['name']->getData();

            $slugify = new Slugify();
            $slug = $slugify->slugify($name);

            $product->setQuantity($form['quantity']->getData());
            $product->setNoTaxePrice($noTaxePrice);
            $product->setAddedDate(new \DateTime('now'));
            $product->setSlug($slug);
            $product->setArchived(false);

            $manager->persist($product);
            $manager->flush();

            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> Le produit a bien été ajouté.'
            );

            return $this->redirectToRoute('adminproducts');
        };

        return $this->render('admin/product/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     * 
     * @Route("/admin/produits/{id}/modifier", name="adminproduct_edit")
     *
     * @param Product $product
     * @return Response
     */
    public function edit(Product $product, Request $request, EntityManagerInterface $manager, PreferenceRepository $pref) {
        $oldFile = $product->getImage();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form['imageFile']->getData()) {
                $file = $form['imageFile']->getData();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $directory = $this->params->get('products_directory');
                $file->move($directory, $fileName);
    
                $product->setImage($fileName);
            }

            $tva = $pref->findOneBy(['id' => 1])->getTaxe();
            $price = $form['taxePrice']->getData();
            $noTaxePrice = $price - (($price / 120) * $tva);
            $name = $form['name']->getData();

            $slugify = new Slugify();
            $slug = $slugify->slugify($name);

            $product->setNoTaxePrice($noTaxePrice);
            $product->setSlug($slug);
            
            $manager->persist($product);
            $manager->flush();

            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> Le produit a bien été modifié.'
            );

            return $this->redirectToRoute("adminproducts");
        }

        return $this->render('admin/product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un produit
     *
     * @Route("/admin/produits/{id}/supprimer", name="adminproduct_delete")
     * 
     * @param Product $product
     * @return Response
     */
    public function delete(Product $product) {
        
        return $this->render('admin/product/delete.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * Permet de supprimer un produit
     *
     * @Route("/admin/produits/{id}/delete", name="adminproduct_suretodelete")
     * 
     * @param Product $product
     * @param ObjectManager $manager
     * @return Response
     */
    public function suretodelete(Product $product, ObjectManager $manager) {

        $product->setArchived(1);
        $product->setSubcategory(null);
        
        $manager->persist($product);
        $manager->flush();

        $this->addFlash(
            'success',
            '<i class="fas fa-check-circle"></i> Le produit a bien été supprimé.'
        );

        return $this->redirectToRoute('adminproducts');
    }
}
