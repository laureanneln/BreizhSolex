<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\PreferenceRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Item;
use App\Repository\ItemRepository;

class ProductController extends AbstractController {
    /**
     * Affichage des produits
     * 
     * @Route("/boutique", name="productspage")
     */
    public function index(ProductRepository $pro, CategoryRepository $cat) {

        $products = $pro->findAll();
        $categories = $cat->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * Affichage d'un produit
     * 
     * @Route("/boutique/produit/{slug}", name="productpage")
     * 
     * @return Response
     */
    public function show(Product $product, CategoryRepository $cat, PreferenceRepository $pref) {

        $categories = $cat->findAll();

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'categories' => $categories,
            'pref' => $pref->findOneBy(array('id' => 1))
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
    public function add(CartRepository $repo, ObjectManager $manager, ProductRepository $pro, ItemRepository $it) {
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

            return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}