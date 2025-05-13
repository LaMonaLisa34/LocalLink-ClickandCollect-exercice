<?php

namespace App\Controller;

use App\Entity\Days;
use App\Entity\Planning;
use App\Entity\BusinessPhotos;
use App\Entity\User;
use App\Repository\CategoriesRepository;
use App\Repository\BusinessRepository;
use App\Service\GeocodingService;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\DaysRepository;
use App\Form\MonCompteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use App\Entity\Business;

class MonCompteController extends AbstractController
{
    #[Route('/user/add_commerce', name: 'add_commerce')]
    public function addCommerce(CategoriesRepository $categoriesRepository, DaysRepository $daysRepository): Response
    {
        $categories = $categoriesRepository->findAll();
        $allDays = $daysRepository->findAll();



        return $this->render('business_crud/new.html.twig', [
            'categories' => $categories,
            'days' => $allDays,
        ]);
    }

    #[Route('/user/businessAdded', name: 'added_commerce')]
    public function businessAdded(
        Request $request,
        BusinessRepository $businessRepository,
        EntityManagerInterface $entityManager,
        GeocodingService $geocodingService,
        Security $security,
        CategoriesRepository $categoriesRepository,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        $csrfToken = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('added_commerce', $csrfToken))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }
        $userConnected = $this->getUser();

        $getall = $request->request->all();
        if (!$userConnected) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter un commerce.');
        }
        $businessNewName = $getall['name'];

        $businesses = $businessRepository->findAll();
        $businessNames = [];
        $business = new Business();
        foreach ($businesses as $busi) {
            $businessName = $busi->getName();
            $businessNames[] = $businessName;

        }
        if (in_array($businessNewName,$businessNames)) {
            $this->addFlash('error', "Ce nom existe déjà, veuillez choisir un autre nom.");
            return $this->redirectToRoute('add_commerce');
        }
        // dd($businessNames);
        $business->setName($getall['name']);
        $business->setPhone($getall['phone']);
        $business->setDescription($getall['description']);
        $category = $getall['category'] ?? null;

        if (!$category) {
            $this->addFlash('error', "Veuillez choisir une catégorie.");
            return $this->redirectToRoute('add_commerce');
        } else {
            $categoryName = $getall['category'];
            $category = $categoriesRepository->findOneBy(['category' => $categoryName]);
            $business->setCategories($category);
        }




        $business->setStreetNumber($getall['streetNumber']);
        $business->setStreetName($getall['streetName']);
        $business->setCityName($getall['CityName']);

        $streetNumber = $business->getStreetNumber();
        $streeName = $business->getStreetName();
        $cityName = $business->getCityName();

        $address = "$streetNumber " . "$streeName, " . "$cityName, " . "France";
        $coordinates = $geocodingService->getCoordinates($address);
        if (!$coordinates) {
            $this->addFlash('errorAddress', "L'adresse est invalide.");
            return $this->redirectToRoute('add_commerce');
        } else {
            $business->setLat($coordinates['lat']);
            $business->setLon($coordinates['lon']);
        }

        if (!$business->getStatistic()) {
            $business->setStatistic(0);
        }

        if (!$business->getFootprintCarbon()) {
            $business->setFootprintCarbon(0.0);
        }

        $currentRoles = $userConnected->getRoles();

        if (!in_array('ROLE_ADMIN', $currentRoles)) {
            $currentRoles = array_filter($currentRoles, function ($role) {
                return $role !== 'ROLE_USER';
            });

            // $currentRoles[] = '';
            $currentRoles[] = 'ROLE_BUSI';
        }
        $userConnected->setRoles($currentRoles);
        $entityManager->persist($userConnected);


        // Forcer a rester connecter après la suppression du commerce -> rafraichir le token
        $security->login($userConnected);
        $business->setOwner($userConnected);

        $business->setValidated(0);

        // Gestion des jours d'ouverture
        $daysRepository = $entityManager->getRepository(Days::class);
        $allDays = $daysRepository->findAll();

        foreach ($allDays as $day) {
            $dayName = $day->getDayName();
            $openingHour = $getall['openingHour' . $dayName];
            $closingHour = $getall['closingHour' . $dayName];

            $validHourPattern = '/^(?:[01]\d|2[0-3]):[0-5]\d$/';

            if ($openingHour !== 'fermé' && !preg_match($validHourPattern, $openingHour)) {
                $this->addFlash('error', "Le format de l'heure d'ouverture pour {$day->getDayName()} est invalide.");
                return $this->redirectToRoute('add_commerce', ['id' => $business->getId()]);
            }

            if ($closingHour !== 'fermé' && !preg_match($validHourPattern, $closingHour)) {
                $this->addFlash('error', "Le format de l'heure de fermeture pour {$day->getDayName()} est invalide.");
                return $this->redirectToRoute('add_commerce', ['id' => $business->getId()]);
            }

            $planning = new Planning();

            $planning->setOpeningHour($openingHour);
            $planning->setClosingHour($closingHour);

            $planning->setDays($day);

            $entityManager->persist($planning);
            $business->addPlanning($planning);
        }

        // Gestion des photos uploadées
        $photoAdded = $request->files;
        $businessName = $getall['name'];
        $busiNameFile = str_replace(' ','',$businessName);
       
	 foreach ($photoAdded as $fileName) {
            if ($fileName) {

                $photoName = uniqid() . "." . $fileName->guessExtension();

                // Déplacer le fichier dans le répertoire des uploads/businessName
                $fileName->move(
                    $this->getParameter('uploadsDirectory') . "/" . $busiNameFile,
                    $photoName
                );

                // Créer une nouvelle entité Photos dans bdd
                $photo = new BusinessPhotos();
                $photo->setPhoto($photoName);
                // $photo->setProduct($product);
                $business->addBusinessPhoto($photo);
                $entityManager->persist($photo);
            }
        }


        $entityManager->persist($business);
        $entityManager->persist($userConnected);

        $entityManager->flush();


        $this->addFlash('success', 'Commerce ajouté avec succès !');
        return $this->redirectToRoute('app_gestionnaire_compte', ['userId' => $userConnected->getId()]);
    }






    #[Route('/user/moncompte', name: 'moncompte')]
    public function moncompte(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Récupère le user connecté 
        /** @var User $user */
        $user = $this->getUser();

        // Si personne n'est connecté, on redirige sur login
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Formulaire basé MonCompteType -> user
        $form = $this->createForm(MonCompteType::class, $user);

        // requete http si formulaire submit
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération du champ plainPassword (non mappé) pour le nouveau mot de passe
            $plainPassword = $form->get('plainPassword')->getData();

            // Si un nouveau mot de passe saisi, hashage et maj de l'entité
            if (!empty($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            // Enregistrement dans la bdd
            $entityManager->persist($user);
            $entityManager->flush();

            // Message flash de confirmation
            $this->addFlash('success', 'Votre compte a bien été mis à jour.');

            // Redirection vers la même page
            return $this->redirectToRoute('moncompte',  ['_fragment' => 'flash']);  // fragment => flash = bloquer la page sur le message flash
        }

        // Rendu de la vue
        return $this->render('mon_compte/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    


    #[Route('/user/moncompte/delete', name: 'app_mon_compte_delete', methods: ['POST'])]
    public function deleteAccount(
        EntityManagerInterface $em, 
        Request $request, 
        TokenStorageInterface $tokenStorage
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
    
        if (!$user) {
            return $this->redirectToRoute('app_homepage');
        }
        // CSRF token pour la sécurité 
        if (!$this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }
        // Recuperer commerce du user
        $business = $em->getRepository(Business::class)->findOneBy(['owner' => $user]);
    
        if ($business) {
            // Verification produit asscoié
            $products = $business->getProducts();
            if ($products) {
                foreach ($products as $product) {
                    $em->remove($product);
                }
                $em->flush(); // Supression des produit
            }
    
            // Supprimer le commerce
            $em->remove($business);
            $em->flush();
        }
    
        // Supprimer le user
        $em->remove($user);
        $em->flush();
    
        // Logout et nettoyage session
        $tokenStorage->setToken(null);
        $request->getSession()->invalidate();
    
        return $this->redirectToRoute('app_homepage');
    }
    
    
    
}
