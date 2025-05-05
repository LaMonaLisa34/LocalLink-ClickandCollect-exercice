<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BusinessRepository;
use App\Repository\UserRepository;


class GestionnaireCompteController extends AbstractController
{   
    #[Route('/user/gestionnaire/compte/{userId}', name: 'app_gestionnaire_compte')]
    public function index(int $userId, UserRepository $userRepository, BusinessRepository $businessRepository): Response
    {
        $user = $userRepository->find($userId);

        if (!$user) {
            throw $this->createNotFoundException("Utilisateur non trouvé.");
        }

        if (!$this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_BUSI') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès interdit.');
        }

        // Recuperer business lié a user 
        $business = $businessRepository->findOneBy(['owner' => $user]);

        return $this->render('gestionnaire_compte/index.html.twig', [
            'user' => $user,
            'business' => $business,
            'businessId' => $business ? $business->getId() : null,
        ]);
    }
    
}
