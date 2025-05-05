<?php

namespace App\Controller;

use App\Repository\BusinessRepository;
use App\Repository\CommandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommandUserHistoryController extends AbstractController
{
    #[Route('/user/{userId}/commands/history', name: 'app_command_user_history')]
    public function index(int $userId, CommandRepository $commandRepository, BusinessRepository $businessRepository): Response
    {
        $commands = $commandRepository->findAll();

        $userCommands = [];
        foreach ($commands as $command) {
                // dd($command->getcarts()[0]->getOwner()->getId());
                if ($command->getcarts()[0]) {

                    if ($command->getcarts()[0]->getOwner()->getId() == $userId) {
                        $userCommands[] = $command;
                    }
                }
        }

        $userCommandData = [];

        foreach ($userCommands as $userCommand) {

            $businessId = $userCommand->getBusiness();
            $commandBusiness = $businessRepository->find($businessId);

            $commandDate = $userCommand->getDateCommand()->format('d-m-Y');
            $commandNumber = $userCommand->getCommandNumber();


            $commandCarts = $userCommand->getCarts();
            $CartData = [];
            foreach ($commandCarts as $cart) {
                // dd($cart->getOwner());
                if ($cart->getOwner()->getId() == $userId) {
                    $CartData[] = $cart;
                }
            }

            $userCommandData[] = [
                'command' => $userCommand,
                'commandNumber' => $commandNumber,
                'date' => $commandDate,
                'cart' => $CartData,
                'business' => $commandBusiness,
            ];
        }

        // dd($carts, $userId);
        // dd($userCommandData);

        return $this->render('command_user_history/command_user_history.html.twig', [
            'userCommand' => $userCommandData
        ]);
    }
}
