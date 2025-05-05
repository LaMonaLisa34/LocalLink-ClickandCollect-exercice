<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\BusinessRepository;

class StatistiqueCommerceController extends AbstractController
{
    #[Route('/business/statistique/commerce/{id}', name: 'app_statistique_commerce')]
    public function index(int $id, BusinessRepository $businessRepository): Response
    {
       
        $business = $businessRepository->find($id);
    
        
        if (!$business) {
            throw $this->createNotFoundException('Le commerce demandé n\'existe pas.');
        }
    
        return $this->render('statistique_commerce/index.html.twig', [
            'controller_name' => 'StatistiqueCommerceController',
            'business' => $business,
        ]);
    }
    
}
