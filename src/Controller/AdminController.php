<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Command;
use App\Form\AdminType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoriesRepository;
use App\Repository\BusinessRepository;
use App\Repository\UserRepository;
use App\Repository\CommandRepository;
use Doctrine\ORM\EntityManagerInterface;


class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function commerceDemandeValidation(
        Request $request,
        EntityManagerInterface $entityManager,
        CategoriesRepository $categoriesRepository,
        BusinessRepository $businessRepository,
        UserRepository $userRepository,
        CommandRepository $commandRepository,

    ): Response {

        $businesses = $businessRepository->findAll();

        // Recuperer commerce qui si non validé 
        $businessData = [];
        foreach ($businesses as $business) {
            // Ignorer les commerces non validés
            if ($business->isValidated() === 1) {
                continue;
            }

            // Récupération des catégories du commerce
            $businessCategory = $business->getCategories() ? $business->getCategories()->getCategory() : null;

            // Calcul des avis
            $reviews = $business->getBusinessReviews()->toArray();
            $totalScore = array_reduce($reviews, fn($carry, $review) => $carry + $review->getBusinessScore(), 0);
            $averageScore = count($reviews) > 0 ? $totalScore / count($reviews) : null;

            // Photos
            $photos = $business->getBusinessPhotos();
            $firstPhoto = !$photos->isEmpty() ? $photos->first()->getPhoto() : null;

            // Compte des utilisateurs
            $userList = $userRepository->findAll();
            $userCount = count($userList);

            // Compte des commandes
            $commandsRepository = $entityManager->getRepository(Command::class);
            $commandCount = $commandRepository->count([]);

            // Adresse
            $address = sprintf(
                '%s %s, %s, France',
                $business->getStreetNumber(),
                $business->getStreetName(),
                $business->getCityName()
            );
            $coordinates = [0, 0];

            // Promotions valides
            $promotions = $business->getPromotions()->toArray();
            $validPromotions = array_filter($promotions, function ($promotion) {
                $now = new \DateTime();
                return $promotion->getBeginDate() <= $now && $promotion->getEndDate() >= $now;
            });
            $businessName = str_replace(' ', '', $business->getName());

            $businessData[] = [
                'business' => $business,
                'businessName' => $businessName,
                'averageScore' => $averageScore,
                'photo' => $firstPhoto,
                'coordinates' => $coordinates,
                'promotions' => $validPromotions,
                'businessCategory' => $businessCategory,

            ];
        }

        // Gestion du formulaire pour ajouter une catégorie
        $categorie = new Categories();
        $formCategory = $this->createForm(AdminType::class, $categorie);
        $formCategory->handleRequest($request);

        if ($formCategory->isSubmitted() && $formCategory->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();

            $this->addFlash('success', 'Nouvelle catégorie créée avec succès.');
            return $this->redirectToRoute('app_admin'); // Redirection après succès
        }

        // Récupération des catégories existantes
        $categories = $categoriesRepository->findAll();

        // Compter boutique valide et invalide
        $unvalidatedCount = $businessRepository->countUnvalidatedBusinesses();
        $validatedCount = $businessRepository->countvalidatedBusinesses();

        // dd($businessData);

        return $this->render('admin/index.html.twig', [
            'businessData' => $businessData,
            'controller_name' => 'AdminController',
            'categoryForm' => $formCategory->createView(),
            'categories' => $categories,
            'userCount' => $userCount,
            'commandCount' => $commandCount,
            'unvalidatedCount' => $unvalidatedCount,
            'validatedCount' => $validatedCount,
        ]);
    }

    #[Route('/admin/category/delete/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function deleteCategory(
        int $id,
        EntityManagerInterface $entityManager,
        CategoriesRepository $categoriesRepository
    ): Response {
        $category = $categoriesRepository->find($id);

        if (!$category) {
            $this->addFlash('error', 'La catégorie n\'existe pas.');
            return $this->redirectToRoute('app_admin');
        }

        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', 'Catégorie supprimée avec succès.');
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/delete-commerce/{id}', name: 'app_delete_commerce', methods: ['POST'])]
    public function delete(
        int $id,
        EntityManagerInterface $entityManager,
        BusinessRepository $businessRepository
    ): Response {
        // Récupération du commerce
        $business = $businessRepository->find($id);

        // Vérifier si le commerce existe
        if (!$business) {
            $this->addFlash('error', 'Le commerce demandé n\'existe pas.');
            return $this->redirectToRoute('app_admin');
        }

        // Suppression produits liés au commerce
        foreach ($business->getProducts() as $product) {
            $entityManager->remove($product);
        }
        $entityManager->flush(); // Supprime les produits avant de continuer

        // Suppression plannings liés au commerce
        foreach ($business->getPlannings() as $planning) {
            $business->removePlanning($planning);
            $entityManager->remove($planning);
        }
        $entityManager->flush();

        // Suppression du commerce
        $entityManager->remove($business);
        $entityManager->flush();

        // Flash
        $this->addFlash('success', 'Commerce et ses données associées supprimés avec succès.');

        return $this->redirectToRoute('app_admin');
    }





    #[Route('/admin/valide-commerce/{id}', name: 'app_valide_commerce', methods: ['POST'])]
    public function validerCommerce(
        int $id,
        EntityManagerInterface $entityManager,
        BusinessRepository $businessRepository
    ): Response {
        // Trouver le commerce par son ID
        $business = $businessRepository->find($id);

        // Maj de "validated" à 1
        $business->setValidated(1);
        $entityManager->persist($business);
        $entityManager->flush();

        // Ajouter un message de confirmation
        $this->addFlash('success', 'Le commerce a été validé avec succès.');
        return $this->redirectToRoute('app_admin');
    }



    #[Route('/admin/category/edit/{id}', name: 'app_category_edit', methods: ['POST'])]
    public function editCategory(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        CategoriesRepository $categoriesRepository
    ): Response {
        // recuperer la categorie en fonction de lid
        $category = $categoriesRepository->find($id);

        // recuperer nouveau nom de categoris
        $newCategoryName = $request->request->get('category_name');

        // Maj avec le nouveau nom
        $category->setCategory($newCategoryName);
        // Enregistrer dans bdd
        $entityManager->flush();

        // Message de success
        $this->addFlash('success', 'Catégorie modifié avec succès.');
        return $this->redirectToRoute('app_admin');
    }


    #[Route('/listecommercevisible', name: 'liste_commerce_visible', methods: ['POST'])]
    public function getBusinessData(array $businesses, bool $validated): array
    {
        $businessData = [];
        foreach ($businesses as $business) {
            if ($business->isValidated() === 0) {
                continue;
            }

            $photos = $business->getBusinessPhotos();
            $firstPhoto = !$photos->isEmpty() ? $photos->first()->getPhoto() : null;

            $businessData[] = [
                'business' => $business,
                'averageScore' => $this->calculateAverageScore($business),
                'photo' => $firstPhoto,
                'coordinates' => [0, 0],
                'promotions' => array_filter($business->getPromotions()->toArray(), function ($promotion) {
                    $now = new \DateTime();
                    return $promotion->getBeginDate() <= $now && $promotion->getEndDate() >= $now;
                }),
                'businessCategory' => $business->getCategories() ? $business->getCategories()->getCategory() : null,
            ];
        }
        return $businessData;
    }

    #[Route('/rejetercommerce/{id}', name: 'rejeter_commerce', methods: ['POST'])]
    public function rejeterCommerce(
        Request $request,
        int $id,
        EntityManagerInterface $entityManager,
        BusinessRepository $businessRepository
    ): Response {
        $commerce = $businessRepository->find($id);

        if (!$commerce) {
            throw $this->createNotFoundException('Commerce non trouvé.');
        }

        // businness rejection non null alors attente nouveau sumbit du form 
        if ($commerce->getRejectionReason() !== null && !$request->isMethod('POST')) {
            $this->addFlash('error', 'Action impossible : le commerçant doit soumettre à nouveau son commerce.');
            return $this->redirectToRoute('app_admin');
        }

        // Recup raison rejet
        $reasons = $request->request->all('rejection_reasons') ?? [];
        $otherReason = $request->request->get('other_reason', null);

        // Liste formaté des raisons
        $reasonList = implode(', ', $reasons);
        if ($otherReason) {
            $reasonList .= '. Autre : ' . $otherReason;
        }

        // Maj la base de données
        $commerce->setValidated(false);
        $commerce->setRejectionReason($reasonList);
        $entityManager->flush();

        // lien pour modif commerce
        $modifyLink = $this->generateUrl('app_business_c_r_u_d_edit', [
            'id' => $id, // Passer l'ID du commerce
        ]);

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/update-footprint/{id}', name: 'app_admin_update_footprint', methods: ['POST'])]
    public function updateFootprint(
        int $id,
        Request $request,
        BusinessRepository $businessRepository,
        EntityManagerInterface $entityManager
    ): Response {

        // Récupérer le commerce
        $business = $businessRepository->find($id);

        if (!$business) {
            throw $this->createNotFoundException('Commerce non trouvé.');
        }

        // Récupérer la valeur
        $footprintValue = (int) $request->request->get('footprint_carbon');

        // Verifier la la valeur
        $allowedValues = [0, 10, 30, 60, 100];

        // MAJ nouvelle valeur bdd
        $business->setFootprintCarbon($footprintValue);

        // Sauvegarde en base
        $entityManager->flush();

        return $this->redirectToRoute('app_admin');
    }

    private function calculateAverageScore($business): ?float
    {
        $reviews = $business->getBusinessReviews()->toArray();
        $totalScore = array_reduce($reviews, fn($carry, $review) => $carry + $review->getBusinessScore(), 0);
        return count($reviews) > 0 ? $totalScore / count($reviews) : null;
    }
}
