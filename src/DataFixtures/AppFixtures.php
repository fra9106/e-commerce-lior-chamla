<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Category;
use Faker\Factory;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public function __construct(SluggerInterface $slugger,UserPasswordEncoderInterface $encoder )
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }
    
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        $admin = new User();
        $password = $this->encoder->encodePassword($admin,'admin');
        $admin->setEmail("admin@gmail.com")
        ->setPassword($password)
        ->setFirstName($faker->firstNameFemale())
        ->setLastName("Admin")
        ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        for ( $u = 0; $u < 5; $u++){
            $user = new User();
            $password = $this->encoder->encodePassword($user,'toto');
            $user->setEmail("user$u@gmail.com")
            ->setFirstName($faker->firstNameMale())
            ->setLastName($faker->lastName())
            ->setPassword($password);

            $manager->persist($user);
        }
        
        for ( $c = 0; $c < 3; $c++){
            $category = new Category();
            $category->setName($faker->department)
            ->setSlug(strtolower($this->slugger->slug($category->getName())));
            
            $manager->persist($category);
            
            for ($p = 0; $p < mt_rand(15,20); $p++){
                $product = new Product();
                $product->setName($faker->productName)
                //->setPrice(mt_rand(100,200)) quantité aléatoire entre 100 et 200
                ->setPrice($faker->price(4000,20000))
                ->setSlug(strtolower($this->slugger->slug($product->getName())))
                ->setCategory($category)
                ->setShortDescription($faker->paragraph())
                ->setMainPicture($faker->imageUrl(400,400, true))
                ->setCreated(new DateTime());
            ;

                
                $manager->persist($product);
            }
            
        }
        
        
        $manager->flush();
    }
}
