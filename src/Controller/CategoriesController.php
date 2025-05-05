<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Categories;

class CategoriesController extends AbstractController
{
    #[Route('/categories', name: 'app_categories')]
    public function addCategories(EntityManagerInterface $entityManager): Response
    {
        $categoriesList = [
            'Epicerie',
            'Boulangerie',
            'Fleuriste',
            'Boucherie',
            'Coiffeur',
            'Charcuterie'

        ];
    
        foreach ($categoriesList as $categoryName) {
            $category = new Categories();
            $category->setCategory($categoryName);
    
            // Vérifiez si la catégorie existe déjà pour éviter les doublons
            $existingCategory = $entityManager->getRepository(Categories::class)->findOneBy(['category' => $categoryName]);
            if (!$existingCategory) {
                $entityManager->persist($category);
            }
        }
    
        // Sauvegarde dans la base de données
        $entityManager->flush();
    
        return $this->render('categories/index.html.twig', [
            'controller_name' => 'CategorieController',
        ]);
    }
}
