<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public function __construct(SluggerInterface $slugger, UserPasswordEncoderInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        //$faker->addProvider(new \Bezhanov\Faker\Provider\Avatar($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        $admin = new User();
        $password = $this->encoder->encodePassword($admin, 'admin');
        $admin->setEmail("admin@gmail.com")
            ->setPassword($password)
            ->setFirstName($faker->firstNameFemale())
            ->setLastName("Admin")
            ->setRoles(['ROLE_ADMIN'])
            //->setPicture('https://randomuser.me/api/portraits/women/36.jpg')
            ->setBillingAddress($faker->streetAddress)
            ->setPostalCode($faker->postcode)
            ->setPhoneNumber($faker->phoneNumber)
            ->setCity($faker->city);

        $manager->persist($admin);

        $users = [];
        $genres = ['male', 'female'];


        for ($u = 0; $u < 5; $u++) {
            $user = new User();
            $genre = $faker->randomElement($genres);
            //$picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            //$picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;
            $password = $this->encoder->encodePassword($user, 'toto');
            $user->setEmail("user$u@gmail.com")
                ->setFirstName($faker->firstNameMale())
                ->setLastName($faker->lastName())
                ->setPassword($password)
                //->setPicture($picture)
                ->setBillingAddress($faker->streetAddress)
                ->setPostalCode($faker->postcode)
                ->setPhoneNumber($faker->phoneNumber)
                ->setCity($faker->city);

            $users[] = $user;

            $manager->persist($user);
        }

        for ($c = 0; $c < 3; $c++) {
            $category = new Category();
            $category->setName($faker->department)
                ->setSlug(strtolower($this->slugger->slug($category->getName())));

            $manager->persist($category);

            for ($p = 0; $p < mt_rand(15, 20); $p++) {
                $product = new Product();
                $product->setName($faker->productName)
                    //->setPrice(mt_rand(100,200)) quantit?? al??atoire entre 100 et 200
                    ->setPrice($faker->price(4000, 20000))
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph())
                    ->setMainPicture('default.jpg')
                    ->setCreatedAt(new DateTime());;

                $products[] = $product;

                $manager->persist($product);
            }
        }

        for ($p = 0; $p < mt_rand(20, 40); $p++) {
            $purchase = new Purchase;

            $purchase->setFullName($faker->name)
                ->setAddress($faker->streetAddress)
                ->setPostalCode($faker->postcode)
                ->setCity($faker->city)
                ->setUser($faker->randomElement($users))
                ->setTotal(mt_rand(2000, 30000))
                ->setPurchaseAt($faker->dateTimeBetween('-6 months'));

            $selectedProducts = $faker->randomElements($products, mt_rand(3, 5));

            foreach ($selectedProducts as $product) {
                $purchaseItem = new PurchaseItem;
                $purchaseItem->setProduct($product)
                    ->setQuantity(mt_rand(1, 3))
                    ->setProductName($product->getName())
                    ->setProductPrice($product->getPrice())
                    ->setTotal(
                        $purchaseItem->getProductPrice() * $purchaseItem->getQuantity()
                    )
                    ->setPurchase($purchase);

                $manager->persist($purchaseItem);
            }

            if ($faker->boolean(90)) {
                $purchase->setStatus(Purchase::STATUS_PAID);
            }

            $manager->persist($purchase);
        }



        $manager->flush();
    }
}
