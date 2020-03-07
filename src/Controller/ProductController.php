<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\ItemRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use App\Repository\PreferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController {
    /**
     * Affichage des produits
     * 
     * @Route("/boutique", name="productspage")
     */
    public function index(ProductRepository $pro, CategoryRepository $cat, CartRepository $repo) {

        $products = $pro->findAll();
        $categories = $cat->findAll();

        $outOfStock = [];

        if ($this->getUser()) {

            foreach ($products as $product) {
                if ($product->getQuantity() == 0) {
                    array_push($outOfStock, $product->getId());
                }
                $leftQuantity = $product->getQuantity();
    
                $cart = $repo->findOneBy(['user' => $this->getUser()]);
                
                foreach($cart->getItems() as $item) {
                    if ($item->getProduct()->getId() == $product->getId()) {
                        
                        $leftQuantity = $leftQuantity - $item->getQuantity();
    
                        if ($leftQuantity == 0) {
                            array_push($outOfStock, $product->getId());
                        }
                    }
                }
    
            }
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'outOfStock' => $outOfStock
        ]);
    }

    /**
     * Affichage d'un produit
     * 
     * @Route("/boutique/produit/{slug}", name="productpage")
     * 
     * @return Response
     */
    public function show(Product $product, CategoryRepository $cat, PreferenceRepository $pref, CartRepository $repo) {

        $categories = $cat->findAll();

        $leftQuantity = $product->getQuantity();

        if ($repo->findOneBy(['user' => $this->getUser()])) {
            $cart = $repo->findOneBy(['user' => $this->getUser()]);
        
            foreach($cart->getItems() as $item) {
                if ($item->getProduct()->getId() == $product->getId()) {
                    $leftQuantity = $leftQuantity - $item->getQuantity();

                    break;
                }
            }
        }
        

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'categories' => $categories,
            'pref' => $pref->findOneBy(array('id' => 1)),
            'leftQuantity' => $leftQuantity
        ]);
    }

    /**
     * Permet d'ajouter un produit au panier
     * 
     * @Route("/boutique/produit/{id}/ajouter", name="product_add")
     * 
     * @param Item $item
     * @return Response
     */
    public function add(CartRepository $repo, EntityManagerInterface $manager, ProductRepository $pro, ItemRepository $it) {
            $cart = $repo->findOneBy(['user' => $this->getUser()]);

            $quantity = $_GET['quantity'];
            $product = $pro->findOneBy(['id' => $_GET['product_id']]);
            $totalPrice = $product->getTaxePrice() * $quantity;

            $new = true;

                foreach($cart->getItems() as $item) {
                    if($item->getProduct()->getId() == $_GET['product_id']) {
                        $sameItem = $it->findOneByProductAndCart($product, $cart);

                        $sameItem->setQuantity($sameItem->getQuantity() + 1);
                        $manager->persist($sameItem);
                        $manager->flush();

                        $new = false;
                        break;
                    }
                }

                if ($new) {
                    $item = new Item();

                    $item->setCart($cart)
                            ->setProduct($product)
                            ->setQuantity($quantity)
                            ->setTotalPrice($totalPrice);
                        
                    $manager->persist($item);
                    $manager->flush();
                }

            $this->addFlash(
                'success',
                '<i class="fas fa-check-circle"></i> Le produit a bien été ajouté au panier.'
            );

            return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}