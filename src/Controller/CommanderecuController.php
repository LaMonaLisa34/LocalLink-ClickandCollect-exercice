<?php

namespace App\Controller;

use App\Repository\BusinessRepository;
use App\Repository\CommandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CommanderecuController extends AbstractController
{
    #[Route('/business/commandes-recu/{businessId}', name: 'app_commandes_recu')]
    public function index(int $businessId, BusinessRepository $businessRepository): Response
    {
        $business = $businessRepository->find($businessId);
        $commands = $business->getCommands();

        $BusinessCommandData = [];
        foreach ($commands as $command) {
            // dd($command);

            $carts = $command->getCarts();
            $commandDate = $command->getDateCommand()->format('d-m-Y');
            $commandNumber = $command->getCommandNumber();


            $CartData = [];
            foreach ($carts as $cart) {
                $CartData[] = $cart;
            }

            $BusinessCommandData[] = [
                'command' => $command,
                'commandNumber' => $commandNumber,
                'statusCommand' => $command->getStatus(),
                'commandId' => $command->getId(),
                'date' => $commandDate,
                'cart' => $CartData,
                'business' => $business,
            ];
        }

        return $this->render('commanderecu/commandBusiness.html.twig', [
            'business' => $business,
            'commands' => $BusinessCommandData
        ]);
    }

    #[Route('/business/commandesRecu/statusUpdated/{businessId}', name: 'app_commandes_status_updated')]
    public function statusUpdated(int $businessId, Request $request, EntityManagerInterface $entityManager, CommandRepository $commandRepository, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $csrfToken = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('app_commandes_updated', $csrfToken))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $getAll = $request->request->all();
        // dd($getAll);
        
        $command = $commandRepository->find($getAll['commandId']);
        $command->setStatus($getAll['status']);
        $entityManager->persist($command);
        $entityManager->flush();

        return $this->redirectToRoute('app_commandes_recu', [
            'businessId' => $businessId
        ]);
    }
}
