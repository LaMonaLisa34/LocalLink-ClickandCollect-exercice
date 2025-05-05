<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Repository\BusinessRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

class CartController extends AbstractController
{
    #[Route('/user/{userId}/cart', name: 'app_cart')]
    public function index(string $userId, CartRepository $cartRepository, UserRepository $userRepository): Response
    {

        $users = $userRepository->findAll(); //somehow mandatory ?!?
        $carts = $cartRepository->findAll();

        $cartData = [];
        $quantity = null;
        $multPrice = null;
        $totalPrice = null;

        foreach ($carts as $cart) {
            $user = $cart->getOwner()->getId();
            if ($user == $userId) {
                $cartId = $cart->getId();
                $quantity = $cart->getQuantity();
                $multPrice = $cart->getProduct()->getPrice() * $quantity;
                $product = $cart->getProduct();
                $command = $cart->getCommand();

                // dd($command);
                $productQuantity = $product->getQuantity();
                // dump($quantity, $productQuantity);
                $productFirstPhoto = $product->getPhotos()->first()->getPhoto();
                $cartData[] = [
                    'cartId' => $cartId,
                    'quantity' => $quantity,
                    'maxQuantity' => $quantity + $productQuantity,
                    'business' => $cart->getBusiness()->getName(),
                    'busiNameFile' => str_replace(' ','',$cart->getBusiness()->getName()),
                    'product' => $cart->getProduct(),
                    'productPhoto' => $productFirstPhoto,
                    'multPrice' => $multPrice,
                    'owner' => $userId,
                    'command' => $command,
                ];
                $totalPrice += $multPrice;
            }
        }

        $allBusiness = [];
        foreach ($cartData as $cartRef) {
                if (!$cartRef['command']) {
                $allBusiness[$cartRef['business']][] = $cartRef;
            }
        }

        // dd($allBusiness);
        return $this->render('cart/cart.html.twig', [
            'allBusiness' => $allBusiness,
            'totalPrice' => $totalPrice,
            'userId' => $userId
        ]);
    }


    #[Route('/user/{userId}/cart/update', name: 'app_cart_update', methods: ['POST'])]
    public function update(string $userId, CartRepository $cartRepository, Request $request, CsrfTokenManagerInterface $csrfTokenManager, EntityManagerInterface $entityManager): Response
    {

        $csrfToken = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('cart_update', $csrfToken))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $cartsId = $request->request->all('products');
        foreach ($cartsId as $key => $cartId) {
            $cart = $cartRepository->find($key);

            $oldCartQuantity = $cart->getQuantity();
            $cart->setQuantity($cartId);
            $entityManager->persist($cart);
            $entityManager->flush();

            $cartQuantity = $cart->getQuantity();
            $product = $cart->getProduct();
            $productQuantity = $product->getQuantity();
            if ($oldCartQuantity < $cartQuantity) {
                $product->setQuantity($productQuantity - ($cartQuantity - $oldCartQuantity));
            } else {
                $product->setQuantity($productQuantity + ($oldCartQuantity - $cartQuantity));
            }
            // dump($oldCartQuantity,$cartQuantity);

            $entityManager->persist($product);
            $entityManager->flush();
        }


        return $this->redirectToRoute('app_cart', ['userId' => $userId]);
    }

    #[Route('/user/{userId}/cart/{cartId}/delete', name: 'app_cart_delete', methods: ['POST'])]
    public function delete(string $userId, string $cartId, CartRepository $cartRepository, Request $request, CsrfTokenManagerInterface $csrfTokenManager, EntityManagerInterface $entityManager): Response
    {

        $csrfToken = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('cart_update', $csrfToken))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $cart = $cartRepository->find($cartId);
        $cartQuantity = $cart->getQuantity();
        $product = $cart->getProduct();
        $productQuantity = $product->getQuantity();
        
        $product->setQuantity($productQuantity+$cartQuantity);

        $entityManager->remove($cart);
        $entityManager->flush();

        return $this->redirectToRoute('app_cart', ['userId' => $userId]);
    }

    #[Route('/user/{userId}/cart/{businessId}/{productId}/add', name: 'app_cart_add', methods: ['POST'])]
    public function add(string $userId, string $productId, string $businessId, CartRepository $cartRepository, UserRepository $userRepository, BusinessRepository $businessRepository, ProductRepository $productRepository, Request $request, CsrfTokenManagerInterface $csrfTokenManager, EntityManagerInterface $entityManager): Response
    {

        $csrfToken = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('cart_add', $csrfToken))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }


        $carts = $cartRepository->findAll();
        foreach ($carts as $cartRef) {
            $cartProductId = $cartRef->getProduct()->getId();
            if ($cartProductId == $productId && $cartRef->getCommand() == null) {

                $cartOldQuantity = $cartRef->getQuantity();
                $quantity = $request->get('quantity');

                $product = $productRepository->find($productId);
                $owner = $userRepository->find($userId);
                $business = $businessRepository->find($businessId);
                $businessId = $business->getId();

                $productQuantity = $product->getQuantity();
                $product->setQuantity($productQuantity - $quantity);

                $cartRef->setOwner($owner);
                $cartRef->setBusiness($business);
                $cartRef->setProduct($product);
                $cartRef->setQuantity($quantity + $cartOldQuantity);


                $entityManager->persist($cartRef);
                $entityManager->persist($product);
                $entityManager->flush();

                return $this->redirectToRoute('app_business', ['id' => $businessId, 'userId' => $userId]);
            }
        }

        $quantity = $request->get('quantity');

        $product = $productRepository->find($productId);
        $owner = $userRepository->find($userId);
        $business = $businessRepository->find($businessId);
        $businessId = $business->getId();

        $cart = new Cart();
        $cart->setOwner($owner);
        $cart->setBusiness($business);
        $cart->setProduct($product);
        $cart->setQuantity($quantity);
        // $cart->setCommand(null);

        $productQuantity = $product->getQuantity();
        $product->setQuantity($productQuantity - $quantity);

        $entityManager->persist($cart);
        $entityManager->persist($product);
        $entityManager->flush();

        return $this->redirectToRoute('app_business', ['id' => $businessId, 'userId' => $userId]);
    }
}
