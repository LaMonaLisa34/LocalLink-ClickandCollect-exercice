<?php

namespace App\Controller;

use App\Entity\BusinessReview;
use App\Repository\BusinessRepository;
use App\Repository\BusinessReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

class BusinessReviewController extends AbstractController
{
    #[Route('/home/business/{id}/review', name: 'app_business_review')]
    public function index(string $id, BusinessRepository $businessRepository, Security $security): Response
    {
        $business = $businessRepository->find($id);
        $businessName = str_replace(' ','',$business->getName());


        $reviews = $business->getBusinessReviews();
        $reviewsCount = count($reviews);
        $reviewsData = [];
        $users = [];

        if ($reviewsCount > 0) {
            foreach ($reviews as $review) {

                $reviewScore = $review->getBusinessScore();
                $users[] = $review->getUser()->getId();

                $reviewsData[] = [
                    'data' => $review,
                    'score' => round($reviewScore),
                ];
            }
        }

        $photos = $business->getBusinessPhotos();

        $firstPhoto = null;
        if (!$photos->isEmpty()) {
            $firstPhoto = $photos->first()->getPhoto();
        }

        $user = $security->getUser();
        if (!$user) {
            $user = null;
        }

        $userConnected = $this->getUser();
        $businessId = null;
        if ($userConnected) {

            $businesses = $userConnected->getBusiness();
            foreach ($businesses as $busi) {
                $businessId = $busi->getId();
            }
        }

        // dd($business);

        return $this->render('business_review/businessReview.html.twig', [
            'business' => $business,
            'businessName' => $businessName,
            'reviewsData' => $reviewsData,
            'firstPhoto' => $firstPhoto,
            'user' => $user,
            'allUsers' => $users,
            'businessId' => $businessId,
        ]);
    }

    #[Route('/user/{id}/review/updated/', name: 'app_business_review_updated')]
    public function update(Request $request, string $id, BusinessRepository $businessRepository, EntityManagerInterface $manager): Response
    {

        if ($request->isMethod('POST')) {

            $business = $businessRepository->find($id);

            if (!$business) {
                throw $this->createNotFoundException('Commerce non trouvé.');
            }
            if (!$request->isMethod('POST')) {
                return $this->redirectToRoute('app_business_review', ['id' => $id]);
            }

            $message = $request->get('message');
            $score = $request->get('score');
            $reply = $request->get('reply');
            $reviewId = $request->get('review_id');
            // dd($reviewId);

            if (!$reply) {

                if (empty($score)) {
                    $this->addFlash('error', 'Vos avis doit au moins contenir une note');
                    return $this->redirectToRoute('app_business_review', ['id' => $id]);
                }

                $businessReview = new BusinessReview();
                $businessReview->setBusiness($business);
                $businessReview->setComment($message);
                $businessReview->setBusinessScore($score);
                $businessReview->setUser($this->getUser());

                $business->addBusinessReview($businessReview);

                $this->addFlash('success', "L'avis a été ajouté avec succès.");

                $manager->persist($businessReview);
                $manager->flush();
            } else {

                $reviews = $business->getBusinessReviews();
                // $reviewsData = [];

                foreach ($reviews as $review) {
                    $review_Id = $review->getId();
                    if ($review_Id == $reviewId) {
                        $review->setResponse($reply);
                        $this->addFlash('success', "La réponse a été ajouté avec succès.");

                        $manager->persist($review);
                        $manager->flush();
                    }
                }
            }
            // dd($id);
            return $this->redirectToRoute('app_business_review', ['id' => $id]);
        }
    }


    #[Route('/user/{businessId}/review/delete/{reviewId}', name: 'business_review_delete', methods: ['POST', 'DELETE'])]
    public function deleteReview(int $businessId, int $reviewId, EntityManagerInterface $entityManager, Request $request, CsrfTokenManagerInterface $csrfTokenManager, BusinessReviewRepository $businessRepository): Response
    {
        $token = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete_review_' . $reviewId, $token))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $review = $businessRepository->find($reviewId);

        if (!$review || $review->getBusiness()->getId() !== $businessId) {
            throw $this->createNotFoundException('Avis non trouvé pour ce commerce.');
        }

        if ($review) {
            $this->addFlash('success', "L'avis a été supprimés avec succès.");
            $entityManager->remove($review);
            $entityManager->flush();
        } else {
            $this->addFlash('error', "La suppression de l'avis a échoué");
        }


        return $this->redirectToRoute('app_business_review', ['id' => $businessId]);
    }

    #[Route('/user/{businessId}/review/{reviewId}/reply/delete/', name: 'business_reply_delete', methods: ['POST'])]
    public function deleteReply(int $businessId, int $reviewId, EntityManagerInterface $entityManager, Request $request, CsrfTokenManagerInterface $csrfTokenManager, BusinessReviewRepository $businessRepository): Response
    {
        $token = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete_reply_' . $reviewId, $token))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $review = $businessRepository->find($reviewId);
        if ($review) {
            $review->setResponse(null);
            $entityManager->flush();
            $this->addFlash('success', 'La réponse a été supprimés avec succès.');
        } else {
            $this->addFlash('error', "La suppression de la réponse a échoué");
        }

        if (!$review || $review->getBusiness()->getId() !== $businessId) {
            throw $this->createNotFoundException('Avis non trouvé pour ce commerce.');
        }

        return $this->redirectToRoute('app_business_review', ['id' => $businessId]);
    }


    #[Route('/admin/business/{businessId}/review/bulk_delete', name: 'admin_reviews_bulk_delete', methods: ['POST'])]
    public function bulkDeleteReview(
        int $businessId,
        Request $request,
        EntityManagerInterface $entityManager,
        CsrfTokenManagerInterface $csrfTokenManager,
        BusinessReviewRepository $businessRepository
    ): Response {

        $csrfToken = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('bulk_delete_reviews', $csrfToken))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }
        $selectedReviewIds = $request->request->all('selected_reviews');
        // dd($selectedReviewIds);

        if (!empty($selectedReviewIds)) {
            $reviews = $businessRepository->findBy(['id' => $selectedReviewIds]);

            foreach ($reviews as $review) {
                $entityManager->remove($review);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Les avis sélectionnés ont été supprimés avec succès.');
        } else {
            $this->addFlash('error', 'Aucun avis sélectionné.');
        }


        return $this->redirectToRoute('app_business_review', ['id' => $businessId]);
    }
}
