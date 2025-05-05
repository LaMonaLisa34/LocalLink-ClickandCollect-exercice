<?php

namespace App\DataFixtures;

use App\Entity\Business;
use App\Entity\User;
use App\Entity\Categories;
use App\Entity\Label;
use App\Entity\Product;
use App\Entity\ProductReview;
use App\Entity\Planning;
use App\Entity\Days;
use App\Entity\BusinessPhotos;
use App\Entity\Promotions;
use App\Entity\BusinessReview;
use App\Entity\Cart;
use App\Entity\Command;
use App\Entity\Photo;
use App\Entity\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
// use Symfony\Component\Console\Command\Command;

class BusinessFixtures extends Fixture
{

    private $userPasswordHasherInterface;

    public function __construct(UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $streets = [
            'Rue de la Loge',
            'Rue du Pompignan',
            'Rue Saint-Guilhem',
            'Rue de Strasbourg',
            'Avenue du Peyrou',
            'Boulevard du Jardin des Plantes',
            'Rue Henri IV',
            'Rue Foch',
            'Rue Saint-Clément',
            'Rue Saint-Gilles',
            'Rue de la Fraternité',
            'Rue de Grammont',
            'Rue des Arts',
            'Rue des Saules',
            'Rue de l\'Odéon',
            'Rue de la Paix',
            'Rue de l\'Université',
            'Rue de la République',
            'Rue Charles-de-Gaulle',
            'Rue du Cardinal',
        ];
        $categoriesArray = [
            'Epicerie',
            'Boulangerie',
            'Charcuterie',
            'Restaurant',
            'Pizzeria',
            'Bien-être',
            'Huile(s) Essentielle(s)',
            'Maraicher',
            'Informaticien',
        ];

        $roles = [
            "ROLE_USER",
            "ROLE_ADMIN"
        ];

        $rolesTest = [
            "USER",
            "ADMIN",
            "BUSI"
        ];

        $statusCart = [
            'Envoyée',
            'En préparation',
            'Terminée'
        ];


        // Simulate Categories, User, Week, and Label entities
        for ($i = 0; $i < 5; $i++) {
            $categorie = new Categories();
            $categorie->setCategory($faker->randomElement($categoriesArray));
            $manager->persist($categorie);
            $categories[] = $categorie;
        }

        $days = [];
        foreach ($daysOfWeek as $dayName) {
            $day = new Days();
            $day->setDayName($dayName);
            $manager->persist($day);
            $days[] = $day;
        }

        $labels = [];
        for ($i = 0; $i < 5; $i++) {
            $label = new Label();
            $label->setLabel($faker->word);
            $manager->persist($label);
            $labels[] = $label;
        }

        foreach ($roles as $role) {
            $user = new User();
            $user->setFirstName($faker->name);
            $user->setLastName($faker->lastName);
            $user->setEmail($faker->email);
            $user->setPassword($this->userPasswordHasherInterface->hashPassword($user, "1234"));
            $user->setPhone($faker->phoneNumber);
            $user->setRoles([$role]);
            $manager->persist($user);
            $users[] = $user;
        }


        // Generate Businesses
        for ($i = 0; $i < 3; $i++) {

            $owner = new User();
            $owner->setFirstName($faker->name);
            $owner->setLastName($faker->lastName);
            $owner->setEmail($faker->email);
            $owner->setPassword($this->userPasswordHasherInterface->hashPassword($user, "1234"));
            $owner->setPhone($faker->phoneNumber);
            $owner->setRoles(["ROLE_BUSI"]);
            $manager->persist($owner);

            $business = new Business();
            $business->setName($faker->word);
            $business->setStreetNumber("1");
            $business->setStreetName($faker->randomElement($streets));
            $business->setCityName("Montpellier");
            $business->setPhone($faker->phoneNumber);
            $business->setDescription($faker->text);
            $business->setOwner($owner);
            $business->setStatistic(0);
            $business->setFootprintCarbon($faker->randomFloat(2, 0, 100));
            $business->setCategories($faker->randomElement($categories));
            $business->setValidated($faker->boolean(50));
            $business->setLat($faker->randomFloat(6, 43, 44));
            $business->setLon($faker->randomFloat(6, 3.7, 4.1));
            $newFilePath = __DIR__ . '/../../public/uploads/' . str_replace(' ','',$business->getName());
            if (!is_dir($newFilePath)) {
                mkdir($newFilePath, 0777, true);
            }
            $oldPhotoPath = __DIR__ . '/../../public/uploads/' . str_replace(' ','',$business->getName()) . '/dummy-image.jpg';
            $newPhotoPath = __DIR__ . '/../../public/images/home_carousel/dummy-image.jpg';
            copy($newPhotoPath, $oldPhotoPath);

            // Add Labels
            foreach ($labels as $label) {
                if ($faker->boolean(70)) { // 70% chance of adding a label
                    $business->addLabel($label);
                }
            }

            // Add Planning
            foreach ($days as $day) {
                $planning = new Planning();
                $planning->setDays($day);
                $planning->setOpeningHour('08:00');
                $planning->setClosingHour('20:00');
                $planning->addBusiness($business);

                $manager->persist($planning);
            }

            // Add Products
            for ($j = 0; $j < 8; $j++) {


                $product = new Product();
                $product->setName($faker->word);
                $product->setDescription($faker->sentence);
                $product->setPrice($faker->randomFloat(2, 10, 100));
                $product->setQuantity($faker->numberBetween(0, 100));
                $product->setBusiness($business);
                for ($k = 0; $k < $faker->numberBetween(2, 3); $k++) {
                    $productphoto = new Photo();
                    $productphoto->setPhoto("dummy-image.jpg");
                    $manager->persist($productphoto);
                    $product->addPhoto($productphoto);
                    $newFilePathProd = __DIR__ . '/../../public/uploads/' . str_replace(' ','',$business->getName()) . '/products';
                    if (!is_dir($newFilePathProd)) {
                        mkdir($newFilePathProd, 0777);
                    }
                    $oldPhotoPathProd = __DIR__ . '/../../public/uploads/' . str_replace(' ','',$business->getName()) . '/products/dummy-image.jpg';
                    $newPhotoPathProd = __DIR__ . '/../../public/images/home_carousel/dummy-image.jpg';
                    copy($newPhotoPathProd, $oldPhotoPathProd);

                    $manager->persist($product);
                    $business->addProduct($product);
                    // $cart->addProductCart($product);


                    for ($k = 0; $k < $faker->numberBetween(2, 4); $k++) {
                        $productReview = new ProductReview();
                        $productReview->setScore($faker->numberBetween(1, 5));
                        $productReview->setUser($faker->randomElement($users));
                        $manager->persist($productReview);
                        $product->addProductReview($productReview);
                    }
                }
                $productArray[] = $product;
            }

            // Add commands
            $order = new Command();
            $order->setDateCommand($faker->dateTime);
            $order->setTotalPrice($faker->randomFloat(2, 5, 50));
            $order->setStatus($faker->randomElement($statusCart));
            $order->setCommandNumber($faker->numberBetween(1, 100) . $faker->randomLetter());
            $order->setBusiness($business);

            $manager->persist($order);

            for ($j = 0; $j < $faker->numberBetween(1, 4); $j++) {
                // Add carts
                $cart = new Cart();
                $cart->setOwner($owner);
                $cart->setBusiness($business);
                $cart->setProduct($faker->randomElement($productArray));
                $cart->setQuantity($faker->numberBetween(1, 4));
                $cart->setCommand($order);


                $manager->persist($cart);
            }


            // Add Business Photos
            for ($j = 0; $j < $faker->numberBetween(1, 3); $j++) {
                $photo = new BusinessPhotos();
                $photo->setPhoto("dummy-image.jpg");
                $photo->setBusiness($business);
                $manager->persist($photo);
                $business->addBusinessPhoto($photo);
            }

            // Add Promotions
            for ($j = 0; $j < $faker->numberBetween(1, 3); $j++) {
                $promotion = new Promotions();
                $promotion->setPromotionName($faker->word);
                $promotion->setPromotionDescription($faker->sentence);
                $promotion->setReduction($faker->numberBetween(10, 50));
                $promotion->setBeginDate($faker->dateTime());
                $promotion->setEndDate($faker->dateTime());
                $promotion->setImagePromotion("promo-dummy-image.jpg");
                $promotion->setBusiness($business);

                $newFilePath = __DIR__ . '/../../public/uploads/' . str_replace(' ','',$business->getName()) . '/promotionPhoto';
                if (!is_dir($newFilePath)) {
                    mkdir($newFilePath, 0777);
                }
                $oldPhotoPath = __DIR__ . '/../../public/uploads/' . str_replace(' ','',$business->getName()) . '/promotionPhoto/promo-dummy-image.jpg';
                $newPhotoPath = __DIR__ . '/../../public/images/home_carousel/dummy-image.jpg';
                copy($newPhotoPath, $oldPhotoPath);

                $manager->persist($promotion);
                $business->addPromotion($promotion);
            }


            // {% set imagePath = 'uploads/' ~ promotion.businessName ~ '/promotionPhoto/' ~ promotion.promotionPhoto %}


            // Add Business Reviews
            for ($j = 0; $j < $faker->numberBetween(1, 5); $j++) {
                $review = new BusinessReview();
                $review->setBusinessScore($faker->numberBetween(1, 5));
                $review->setComment($faker->sentence);
                $review->setBusiness($business);
                $review->setUser($faker->randomElement($users));
                $manager->persist($review);
                $business->addBusinessReview($review);
            }

            // Add Business Events
            for ($j = 0; $j < $faker->numberBetween(0, 5); $j++) {
                $event = new Events();
                $event->setNameEvent($faker->word);
                $event->setDescriptionEvent($faker->sentence);
                $event->setBeginDate($faker->dateTime());
                $event->setEndDate($faker->dateTime());
                $manager->persist($event);
                $business->addEvent($event);
            }

            $manager->persist($business);
            $manager->flush();
        }


        //TEST USERS
        foreach ($rolesTest as $role) {
            $user = new User();
            $user->setFirstName($role);
            $user->setLastName('test');
            $user->setEmail(strtolower($role) . '@test.com');
            $user->setPassword($this->userPasswordHasherInterface->hashPassword($user, "1234"));
            $user->setPhone($faker->phoneNumber);
            $user->setRoles(['ROLE_' . $role]);
            $manager->persist($user);
            if ($role == 'BUSI') {

                $business = new Business();
                $business->setName('Test Business');
                $business->setStreetNumber("1");
                $business->setStreetName($faker->randomElement($streets));
                $business->setCityName("Montpellier");
                $business->setPhone($faker->phoneNumber);
                $business->setDescription($faker->text);
                $business->setOwner($user);
                $business->setStatistic(0);
                $business->setFootprintCarbon($faker->randomFloat(2, 0, 100));
                $business->setCategories($faker->randomElement($categories));
                $business->setValidated(true);
                $business->setLat($faker->randomFloat(6, 43, 44));
                $business->setLon($faker->randomFloat(6, 3.7, 4.1));
                $newFilePath = __DIR__ . '/../../public/uploads/' . str_replace(' ','',$business->getName());
                if (!is_dir($newFilePath)) {
                    mkdir($newFilePath, 0777, true);
                }
                $oldPhotoPath = __DIR__ . '/../../public/uploads/' . str_replace(' ','',$business->getName()) . '/dummy-image.jpg';
                $newPhotoPath = __DIR__ . '/../../public/images/home_carousel/dummy-image.jpg';
                copy($newPhotoPath, $oldPhotoPath);

                // Add Labels
                foreach ($labels as $label) {
                    if ($faker->boolean(70)) { // 70% chance of adding a label
                        $business->addLabel($label);
                    }
                }

                // Add Planning
                foreach ($days as $day) {
                    $planning = new Planning();
                    $planning->setDays($day);
                    $planning->setOpeningHour('08:00');
                    $planning->setClosingHour('20:00');
                    $planning->addBusiness($business);
                    $manager->persist($planning);
                }

                // Add Products
                for ($j = 0; $j < 8; $j++) {

                    $product = new Product();
                    $product->setName($faker->word);
                    $product->setDescription($faker->sentence);
                    $product->setPrice($faker->randomFloat(2, 10, 100));
                    $product->setQuantity($faker->numberBetween(0, 100));
                    $product->setBusiness($business);
                    for ($k = 0; $k < $faker->numberBetween(2, 3); $k++) {
                        $productphoto = new Photo();
                        $productphoto->setPhoto("dummy-image.jpg");
                        $manager->persist($productphoto);
                        $product->addPhoto($productphoto);
                        $newFilePathProd = __DIR__ . '/../../public/uploads/' . str_replace(' ','',$business->getName()) . '/products';
                        if (!is_dir($newFilePathProd)) {
                            mkdir($newFilePathProd, 0777);
                        }
                        $oldPhotoPathProd = __DIR__ . '/../../public/uploads/' . str_replace(' ','',$business->getName()) . '/products/dummy-image.jpg';
                        $newPhotoPathProd = __DIR__ . '/../../public/images/home_carousel/dummy-image.jpg';
                        copy($newPhotoPathProd, $oldPhotoPathProd);

                        $manager->persist($product);
                        $business->addProduct($product);
                        // $cart->addProductCart($product);


                        for ($k = 0; $k < $faker->numberBetween(2, 4); $k++) {
                            $productReview = new ProductReview();
                            $productReview->setScore($faker->numberBetween(1, 5));
                            $productReview->setUser($faker->randomElement($users));
                            $manager->persist($productReview);
                            $product->addProductReview($productReview);
                        }
                    }
                }

                // Add commands
                $order = new Command();
                $order->setDateCommand($faker->dateTime);
                $order->setTotalPrice($faker->randomFloat(2, 5, 50));
                $order->setStatus($faker->randomElement($statusCart));
                $order->setCommandNumber($faker->numberBetween(1, 100) . $faker->randomLetter());
                $order->setBusiness($business);


                $manager->persist($order);

                // Add carts
                $cart = new Cart();
                $cart->setOwner($owner);
                $cart->setBusiness($business);
                $cart->setProduct($faker->randomElement($productArray));
                $cart->setQuantity($faker->numberBetween(1, 4));
                $cart->setCommand($order);

                $manager->persist($cart);


                // Add Business Photos
                for ($j = 0; $j < $faker->numberBetween(1, 3); $j++) {
                    $photo = new BusinessPhotos();
                    $photo->setPhoto("dummy-image.jpg");
                    $photo->setBusiness($business);
                    $manager->persist($photo);
                    $business->addBusinessPhoto($photo);
                }

                // Add Promotions
                for ($j = 0; $j < $faker->numberBetween(1, 3); $j++) {
                    $promotion = new Promotions();
                    $promotion->setPromotionName($faker->word);
                    $promotion->setPromotionDescription($faker->sentence);
                    $promotion->setReduction($faker->numberBetween(10, 50));
                    $promotion->setBeginDate($faker->dateTime());
                    $promotion->setEndDate($faker->dateTime());
                    $promotion->setImagePromotion("promo-dummy-image.jpg");
                    $promotion->setBusiness($business);

                    $newFilePath = __DIR__ . '/../../public/uploads/' . str_replace(' ','',$business->getName()) . '/promotionPhoto';
                    if (!is_dir($newFilePath)) {
                        mkdir($newFilePath, 0777);
                    }
                    $oldPhotoPath = __DIR__ . '/../../public/uploads/' . str_replace(' ','',$business->getName()) . '/promotionPhoto/promo-dummy-image.jpg';
                    $newPhotoPath = __DIR__ . '/../../public/images/home_carousel/dummy-image.jpg';
                    copy($newPhotoPath, $oldPhotoPath);

                    $manager->persist($promotion);
                    $business->addPromotion($promotion);
                }

                // Add Business Reviews
                for ($j = 0; $j < $faker->numberBetween(1, 5); $j++) {
                    $review = new BusinessReview();
                    $review->setBusinessScore($faker->numberBetween(1, 5));
                    $review->setComment($faker->sentence);
                    $review->setBusiness($business);
                    $review->setUser($faker->randomElement($users));
                    $manager->persist($review);
                    $business->addBusinessReview($review);
                }

                // Add Business Events
                for ($j = 0; $j < $faker->numberBetween(0, 5); $j++) {
                    $event = new Events();
                    $event->setNameEvent($faker->word);
                    $event->setDescriptionEvent($faker->sentence);
                    $event->setBeginDate($faker->dateTime());
                    $event->setEndDate($faker->dateTime());
                    $manager->persist($event);
                    $business->addEvent($event);
                }

                $manager->persist($business);
                $manager->flush();
            }
        }
    }
}
