<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Cocur\Slugify\Slugify;
use App\Entity\Subcategory;
use App\Form\SubcategoryType;
use App\Repository\CategoryRepository;
use App\Repository\SubcategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminSubcategoryController extends AbstractController
{
    /**
     * Permet d'afficher la liste des catégories
     * @Route("/admin/categories", name="admincategories")
     */
    public function index(CategoryRepository $repo) {

        return $this->render('admin/subcategory/index.html.twig', [
                'categories' => $repo->findBy(
                    array(),
                    array('position' => 'ASC')
                )
            ]);
    }

    /**
     * Permet de créer une catégorie
     *
     * @Route("admin/categories/creer", name="admincategory_create")
     * 
     * @return Response
     */
    public function createCategory(Request $request, ObjectManager $manager, CategoryRepository $cat) {
        $category = new Category();
        $categories = count($cat->findAll());

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $category->setPosition($categories + 1);

            $manager->persist($category);
            $manager->flush();

            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> La catégorie a bien été ajoutée.'
            );

            return $this->redirectToRoute('admincategories');
        } else {
            $errors = $form->getErrors();
            $this->addFlash('danger', '<i class="fas fa-exclamation-circle"></i> La catégorie n\'a pas pu être ajoutée pour une raison inconnue.');
        };

        return $this->render('admin/subcategory/newCategory.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de créer une sous-catégorie
     *
     * @Route("admin/categories/sous-categorie/creer", name="adminsubcategory_create")
     * 
     * @return Response
     */
    public function createSubcategory(Request $request, ObjectManager $manager, SubcategoryRepository $sub) {
        $subcategory = new Subcategory();

        $form = $this->createForm(SubcategoryType::class, $subcategory);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $cat = $form['category']->getData();

            $subcategories = count($sub->findByCategory($cat));
            $slugify = new Slugify();
            $slug = $slugify->slugify($form['name']->getData());

            $subcategory->setPosition($subcategories + 1);
            $subcategory->setSlug($slug);

            $manager->persist($subcategory);
            $manager->flush();

            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> La sous-catégorie a bien été ajoutée.'
            );

            return $this->redirectToRoute('admincategories');
        };

        return $this->render('admin/subcategory/newSubcategory.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/categories/{id}/edit", name="admincategory_edit")
     * 
     * @param Category $cat
     * @return Response
     */
    public function editCategory(CategoryRepository $repo, Category $cat, Request $request, ObjectManager $manager, $id) {
        $count = count($repo->findAll());
        $numbers = [];
        for($i=0; $i<=$count; $i++) {
            array_push($numbers, $i);
        }
        $old = $cat->getPosition(); // Get current position

        $form = $this->createForm(CategoryType::class, $cat, array('numbers'=>$numbers));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $new = $form['position']->getData(); // Get new position
            $cat2 = $repo->findOneByPosition($new); // Find the category with this position
            $cat2->setPosition($old); // Give this category the old position

            $manager->persist($cat);
            $manager->persist($cat2);
            $manager->flush();

            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> La catégorie a bien été modifiée.'
            );

            return $this->redirectToRoute("admincategories");
        }

        return $this->render('admin/subcategory/editCategory.html.twig', [
            'category' => $cat,
            'form' => $form->createView()    
        ]);
    }

    /**
     * @Route("/admin/categories/sous-categories/{id}/edit", name="adminsubcategory_edit")
     * 
     * @param Subcategory $cat
     * @return Response
     */
    public function editSubcategory(SubcategoryRepository $repo, Subcategory $sub, Request $request, ObjectManager $manager, $id) {
        $catId = $sub->getCategory();
        $count = count($repo->findBy(array('category' => $catId->getId())));
        $numbers = [];
        for($i=0; $i<=$count; $i++) {
            array_push($numbers, $i);
        }
        $old = $sub->getPosition(); // Get current position
        $cat = $sub->getCategory();

        $form = $this->createForm(SubcategoryType::class, $sub, array('numbers'=>$numbers));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new = $form['position']->getData(); // Get new position
            $sub2 = $repo->findOneByPosition($new, $cat); // Find the category with this position
            $sub2->setPosition($old); // Give this category the old position

            $slugify = new Slugify();
            $slug = $slugify->slugify($form['name']->getData());

            $sub->setSlug($slug);

            $manager->persist($sub);
            $manager->persist($sub2);
            $manager->flush();

            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> La sous-catégorie a bien été modifiée.'
            );

            return $this->redirectToRoute("admincategories");
        };

        return $this->render('admin/subcategory/editSubcategory.html.twig', [
            'subcategory' => $sub,
            'form' => $form->createView()    
        ]);
    }

    /**
     * Permet de supprimer une catégorie
     *
     * @Route("/admin/categories/{id}/supprimer", name="admincategory_delete")
     * 
     * @param Category $category
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteCategory(Category $category, CategoryRepository $cat, ObjectManager $manager) {
        $manager->remove($category);
        
        $manager->flush();

        $categories = $cat->findAll();
        $i = 1;

        foreach($categories as $category) {
            $category->setPosition($i);
            $i = $i + 1;
            $manager->persist($category);
        }

        $manager->flush();

        $this->addFlash(
            'success',
            '<i class="fas fa-check-circle"></i> La catégorie a bien été supprimée.'
        );

        return $this->redirectToRoute('admincategories');
    }

    /**
     * Permet de supprimer une sous-catégorie
     *
     * @Route("/admin/sous-categories/{id}/supprimer", name="adminsubcategory_delete")
     * 
     * @param Subcategory $subcategory
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteSubcategory(Subcategory $subcategory, SubcategoryRepository $sub, ObjectManager $manager) {
        $manager->remove($subcategory);
        $category = $subcategory->getCategory();
        
        $manager->flush();

        $subcategories = $sub->findByCategory($category->getid());
        $i = 1;

        foreach($subcategories as $subcategory) {
            $subcategory->setPosition($i);
            $i = $i + 1;
            $manager->persist($subcategory);
        }

        $manager->flush();

        $this->addFlash(
            'success',
            '<i class="fas fa-check-circle"></i> La sous-catégorie a bien été supprimée.'
        );

        return $this->redirectToRoute('admincategories');
    }
}
