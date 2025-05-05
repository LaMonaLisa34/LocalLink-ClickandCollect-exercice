<?php

namespace App\Controller;

use App\Entity\ProductReview;
use App\Repository\ProductRepository;
use App\Repository\ProductReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

class ProductReviewController extends AbstractController
{
    #[Route('home/{businessId}/product/{productId}/review', name: 'app_product_review')]
    public function index(string $productId, string $businessId, ProductRepository $productRepository, Security $security): Response
    {
        $product = $productRepository->find($productId);
        $productReviews = $product->getProductReviews();
        $productCount = count($productReviews);

        // dd($productReviews);
        $reviewsData = [];
        $users = [];
        if ($productCount > 0) {
            foreach ($productReviews as $review) {
                $reviewScore = $review->getScore();
                $users[] = $review->getUser()->getId();

                $reviewsData[] = [
                    'data' => $review,
                    'score' => round($reviewScore),
                ];
            }
        }

        $business = $product->getBusiness();
        $photos = $business->getBusinessPhotos();

        $firstPhoto = null;
        if (!$photos->isEmpty()) {
            $firstPhoto = $photos->first()->getPhoto();
        }

        $user = $security->getUser();
        if (!$user) {
            $user = null;
        }
        // dd($reviewsData);

        return $this->render('product_review/productReview.html.twig', [
            'business' => $business,
            'product' => $product,
            'reviewsData' => $reviewsData,
            'firstPhoto' => $firstPhoto,
            'user' => $user,
            'allUsers' => $users,
            'businessId' => $businessId,
        ]);
    }


    #[Route('/user/{businessId}/product/{id}/review/updated/', name: 'app_product_review_updated')]
    public function update(Request $request, string $id, string $businessId, ProductRepository $productRepository, EntityManagerInterface $manager): Response
    {

        if ($request->isMethod('POST')) {

            $product = $productRepository->find($id);

            if (!$product) {
                throw $this->createNotFoundException('Produit non trouvé.');
                return $this->redirectToRoute('app_product_review', ['id' => $id]);
            }
            if (!$request->isMethod('POST')) {
                return $this->redirectToRoute('app_product_review', ['id' => $id]);
            }


            $score = $request->get('score');
            // $reviewId = $request->get('review_id');

            if (empty($score)) {
                $this->addFlash('error', 'Vos avis doit au moins contenir une note');
                return $this->redirectToRoute('app_product_review', ['businessId' => $businessId, 'productId' => $id]);
            }

            
            $productReview = new ProductReview();
            $productReview->setProduct($product);
            $productReview->setScore($score);
            $productReview->setUser($this->getUser());
            
            $product->addProductReview($productReview);
            
            $this->addFlash('success', "L'avis a été ajouté avec succès.");
            $manager->persist($productReview);
            $manager->flush();

            return $this->redirectToRoute('app_product_review', ['businessId' => $businessId, 'productId' => $id]);
        }
    }

    #[Route('/user/{businessId}/product/{productId}/review/delete/{reviewId}', name: 'app_product_review_delete', methods: ['POST', 'DELETE'])]
    public function deleteReview(int $businessId, int $productId, int $reviewId, EntityManagerInterface $entityManager, Request $request, CsrfTokenManagerInterface $csrfTokenManager, ProductReviewRepository $productReviewRepository): Response
    {
        $token = $request->request->get('_token');

        if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete_product_review_' . $reviewId, $token))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $review = $productReviewRepository->find($reviewId);
        if (!$review || $review->getProduct()->getId() !== $productId) {
            throw $this->createNotFoundException('Avis non trouvé pour ce commerce.');
        }
        if($review){
            $this->addFlash('success', "L'avis a été supprimés avec succès.");
            $entityManager->remove($review);
            $entityManager->flush();
        }
        else{
            $this->addFlash('error', "Echec de la suppression.");
        }

        return $this->redirectToRoute('app_product_review', ['businessId' => $businessId, 'productId' => $productId]);
    }

    #[Route('/admin/business/{businessId}/product/{productId}/review/bulk_delete', name: 'admin_product_reviews_bulk_delete', methods: ['POST'])]
    public function bulkDeleteReview(
        int $businessId,
        int $productId,
        Request $request,
        EntityManagerInterface $entityManager,
        CsrfTokenManagerInterface $csrfTokenManager,
        ProductReviewRepository $productRepository
    ): Response {

        $csrfToken = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('bulk_delete_product_reviews', $csrfToken))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }
        $selectedReviewIds = $request->request->all('selected_reviews');

        if (!empty($selectedReviewIds)) {
            $reviews = $productRepository->findBy(['id' => $selectedReviewIds]);

            foreach ($reviews as $review) {
                $entityManager->remove($review);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Les avis sélectionnés ont été supprimés avec succès.');
        } else {
            $this->addFlash('error', 'Aucun avis sélectionné.');
        }


        return $this->redirectToRoute('app_product_review', ['businessId' => $businessId, 'productId' => $productId]);
    }
}
