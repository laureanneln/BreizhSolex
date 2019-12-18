<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\About;
use App\Entity\Title;
use App\Entity\Product;
use App\Entity\Category;
use Cocur\Slugify\Slugify;
use App\Entity\Subcategory;
use App\Entity\Informations;
use App\Entity\Item;
use App\Entity\Order;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture {

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager) {
        $faker = Factory::create('FR-fr');

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $userRole = new Role();
        $userRole->setTitle('ROLE_USER');
        $manager->persist($userRole);

        // Gérer les titres de genre
        $title = new Title();

        $title->setLabel("Madame");
        $manager->persist($title);

        $title = new Title();
        
        $title->setLabel("Monsieur");
        $manager->persist($title);

        // Gérer les onglets "À propos"
        $tab = new About();
        $tab->setName("L'atelier")
            ->setContent($faker->realText(1000, 2));

        $manager->persist($tab);

        $tab = new About();
        $tab->setName("Réparation")
            ->setContent($faker->realText(1500, 2));

        $manager->persist($tab);

        $tab = new About();
        $tab->setName("Restauration")
            ->setContent($faker->realText(2000, 2));

        $manager->persist($tab);

        // Gérer les informations
        $info = new Informations();

        $info->setPhone("0662859670")
            ->setAddress("3 Allée du Bois des Pères")
            ->setZipCode("35135")
            ->setCity("Chantepie");

        $manager->persist($info);

        // Gérer les utilisaturs
        $adminUser = new User();
        $adminUser->setFirstName("Christophe")
            ->setLastName("Gilbert")
            ->setEmail("breizh@solex.com")
            ->setPassword($this->encoder->encodePassword($adminUser, 'password'))
            ->setRegisterDate($faker->dateTimeBetween('-6 months'))
            ->setAddress("10 Rue de norvège")
            ->setAddress2(" ")
            ->setZipCode("35200")
            ->setCity("Rennes")
            ->setPhoneNumber("0102030405")
            ->addUserRole($adminRole);

        $manager->persist($adminUser);

        for($i = 1; $i <= 10; $i++) {
            $user = new User();

            $title = mt_rand(1, 2);
            
            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstname($title == 1 ? 'male' : "female"))
                ->setLastName($faker->lastname)
                ->setEmail($faker->email)
                ->setPassword($hash)
                ->setRegisterDate($faker->dateTimeBetween('-6 months'))
                ->setAddress($faker->streetAddress)
                ->setAddress2(" ")
                ->setZipCode($faker->postcode)
                ->setCity($faker->city)
                ->setPhoneNumber($faker->e164PhoneNumber)
                ->addUserRole($userRole);

                $manager->persist($user);

            
            for($j = 1; j <= mt_rand(1, 3); $j ++) {
                $order = new Order();

                $order->setUserId($user)
                    ->setTotalPrice(1200)
                    ->setDeliveryPrice(3);
                
                $manager->persist($order);

                for($k = 1; $k = mt_rand(3, 5); $k ++) {
                    $item = new Item();

                    $item->setProductId(mr_rand(831, 845))
                        ->setOrderId($order)
                        ->setQuantity(mt_rand(1,4));

                    $manager->persist($item);
                }
            }
        }

        // Gérer les catégories

        for($i = 1; $i < 9; $i++) {
            $category = new Category();

            $name = $faker->sentence(2);
            $position = $i;

            $category->setName($name)
                    ->setPosition($position);

            $rand = mt_rand(1, 4);

            // Gérer les sous-catégories
            for($j = 1; $j < $rand; $j++) {
                $subcategory = new Subcategory();

                $name = $faker->sentence(2);
                $position = $j;

                $slugify = new Slugify();
                $slug = $slugify->slugify($name);

                $subcategory->setName($name)
                            ->setPosition($position)
                            ->setCategory($category)
                            ->setSlug($slug);

                $manager->persist($subcategory);

                // Gérer les produits
                for($k = 1; $k <= mt_rand(3, 8); $k++) {
                    $product = new Product();

                    $image = $faker->imageUrl(1000, 350);
                    $name = $faker->sentence(3); 
                    $reference = $faker->ean8();
                    $noTaxePrice = $faker->randomFloat(1, 10, 30);
                    $taxePrice = $noTaxePrice * (1 + (20 / 100));
                    $quantity = $faker->numberBetween(0, 35);
                    $description = $faker->sentence(10);
                    $height = $faker->randomFloat(1, 0, 10);
                    $width = $faker->randomFloat(1, 0, 10);
                    $weight = $faker->randomFloat(1, 0, 10);
                    $addedDate = $faker->dateTimeBetween('-6 months');

                    $slugify = new Slugify();
                    $slug = $slugify->slugify($name);

                    $product->setImage($image)
                            ->setName($name)
                            ->setReference($reference)
                            ->setNoTaxePrice($noTaxePrice)
                            ->setTaxePrice($taxePrice)
                            ->setQuantity($quantity)
                            ->setDescription($description)
                            ->setHeight($height)
                            ->setWidth($width)
                            ->setWeight($weight)
                            ->setAddedDate($addedDate)
                            ->setSubcategory($subcategory)
                            ->setSlug($slug);

                    $manager->persist($product);
                }
            }
            $manager->persist($category);
        }
        $manager->flush();
    }
}
