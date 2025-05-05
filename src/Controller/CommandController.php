<?php

namespace App\Controller;

use App\Entity\Command;
use App\Repository\CartRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommandController extends AbstractController
{
    #[Route('/user/{userId}/command/create', name: 'app_command')]
    public function index(Request $request, CartRepository $cartRepository, EntityManagerInterface $entityManager): Response
    {
        $getAll = $request->request->all();
        $carts = $request->get('cart');
        foreach ($carts as $cartId) {
            $cart = $cartRepository->find($cartId);
            $business = $cart->getBusiness();
            $businessName = $business->getName();
            $allInBusiness[$businessName][] = $cart;

            $arrayCarts[] = $cart;
        }
        foreach ($allInBusiness as $commandCarts) {
            $command = new Command();

            $now = new DateTime();
            $command->setDateCommand($now);

            $command->setCommandNumber(uniqid());
            $command->setStatus('En préparation');
            
            
            
            $totalPrices = [];
            foreach ($commandCarts as $cart) {
                $business = $cart->getBusiness();
                
                $quantity = $cart->getQuantity();
                $product = $cart->getProduct();
                $priceOneProduct = $product->getPrice();
                $totalPrices[] = $priceOneProduct * $quantity;
                $totalPrice = array_sum($totalPrices);
                
                
                $cart->setCommand($command);
                $entityManager->persist($cart);
            }
            $command->setTotalPrice($totalPrice); 
            $command->setBusiness($business);

            $entityManager->persist($command);
        }
        
        $entityManager->flush();
     
        $this->addFlash('success', "Votre commande a bien été prise en charge.");
        return $this->redirectToRoute('app_homepage');
        
    }
}
