<?php

namespace App\Controller;

use App\Repository\BusinessRepository;
use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\HasVisitedService;
use Doctrine\ORM\EntityManagerInterface;
use function Symfony\Component\Clock\now;

class HomepageController extends AbstractController
{

    #[Route('/', name: 'app_homepage')]
    public function home(BusinessRepository $businessRepository, CategoriesRepository $categoriesRepository): Response
    {

        $businesses = $businessRepository->findAll();

        $allPromotions = [];
        $businessData = [];
        foreach ($businesses as $business) {


            if ($business->isValidated()) {

                //CATEGORY
                $businessCategory = $business->getCategories()->getCategory();

                //REVIEW
                $reviews = $business->getBusinessReviews();
                $totalScore = 0;
                $reviewCount = count($reviews);

                if ($reviewCount > 0) {
                    foreach ($reviews as $review) {
                        $totalScore += $review->getBusinessScore();
                    }
                    $averageScore = $totalScore / $reviewCount;
                } else {
                    $averageScore = null;
                }

                //PHOTOS
                $photos = $business->getBusinessPhotos();

                $firstPhoto = null;
                if (!$photos->isEmpty()) {
                    $firstPhoto = $photos->first()->getPhoto();
                }
                $coordinates = [$business->getLat(), $business->getLon()];

                // PROMOTION
                $promotions = $business->getPromotions();
                $promotionCount = count($promotions);

                $validPromotions = [];
                if ($promotionCount > 0) {
                    foreach ($promotions as $promotion) {
                        $beginDate = $promotion->getBeginDate();
                        $endDate = $promotion->getEndDate();
                        $promotionPhoto = $promotion->getImagePromotion();
                        if ($beginDate <= now() && $endDate >= now()) {
                            $validPromotions[] = $promotion;
                            if ($business->isValidated()) {
                                $allPromotions[] = $promotion;
                            }
                        }
                    }
                }

                $plannings = $business->getPlannings();
                $today = strtolower(date("l"));
                $todayPlanning = [];

                foreach ($plannings as $planning) {
                    $day = $planning->getDays()->getDayName();

                    if (strtolower($day) == $today) {
                        $todayPlanning = [
                            'openingHour' => $planning->getOpeningHour(),
                            'closingHour' => $planning->getClosingHour(),
                        ];
                    }
                }

                $businessData[] = [
                    'business' => $business,
                    'busiNameFile' => str_replace(' ', '', $business->getName()),
                    'averageScore' => round($averageScore),
                    'photo' => $firstPhoto,
                    'coordinates' => $coordinates,
                    'promotions' => $validPromotions,
                    'businessCategory' => $businessCategory,
                    'todayPlanning' => $todayPlanning,
                    'validated' => $business->isValidated(),
                ];
            }
        }
        // dd($businessData);
        $allCategory = [];
        foreach ($businessData as $businessRef) {
            $category = $businessRef['businessCategory'];
            if (!in_array($category, $allCategory, true)) {
                $allCategory[] = $businessRef['businessCategory'];
            }
        }
        foreach ($allCategory as $cat) {
            foreach ($businessData as $businessRef) {
                if ($cat == $businessRef['businessCategory'] && $businessRef['validated'] == true) {
                    $allBusiness[$businessRef['businessCategory']][] = $businessRef;
                }
            }
        }
        // dd($allBusiness);

        $userConnected = $this->getUser();
        $businessId = null;
        if ($userConnected) {

            $businesses = $userConnected->getBusiness();
            foreach ($businesses as $business) {
                $businessId = $business->getId();
            }
        }

        return $this->render('homepage/homepage.html.twig', [
            'businessData' => $businessData,
            'allBusiness' => $allBusiness ?? null,
            'allPromotions' => $allPromotions,
            'businessId' => $businessId
        ]);
    }



    #[Route('/home/business/{id}', name: 'app_business')]
    public function business(BusinessRepository $businessRepository, string $id, HasVisitedService $hasVisitedService, EntityManagerInterface $entityManager): Response
    {
        //BUSINESS
        $business = $businessRepository->find($id);

        $businessName = str_replace(' ', '', $business->getName());
        $businessId = $business->getId();
        $businessReviews = $business->getBusinessReviews();
        $businessTotalScore = 0;
        $reviewCount = count($businessReviews);

        if ($reviewCount > 0) {
            foreach ($businessReviews as $review) {
                $businessTotalScore += $review->getBusinessScore();
            }
            $businessAverageScore = $businessTotalScore / $reviewCount;
        } else {
            $businessAverageScore = null;
        }

        $businessPhotos = $business->getBusinessPhotos();

        $photoData = [];
        foreach ($businessPhotos as $photo) {
            $photoData[] = [
                'photoData' => $photo,
            ];
        }
        $businessFirstPhoto = null;
        if (!$businessPhotos->isEmpty()) {
            $businessFirstPhoto = $businessPhotos->first()->getPhoto();
        }

        $businessCategory = $business->getCategories()->getCategory();

        //PLANNING
        $plannings = $business->getPlannings();
        $planningData = [];

        $index = 0;
        $week = [
            'Lundi',
            'Mardi',
            'Mercredi',
            'Jeudi',
            'Vendredi',
            'Samedi',
            'Dimanche',
        ];

        foreach ($plannings as $planning) {
            $day = $planning->getDays()->getDayName();
            $planningData[] = [
                'planningData' => $planning,
                'jour' => $week[$index],
            ];
            $index++;
        }

        // PROMOTION
        $promotions = $business->getPromotions();
        $promotionCount = count($promotions);
        $promotionData = [];

        $validPromotions = [];
        if ($promotionCount > 0) {
            foreach ($promotions as $promotion) {
                $beginDate = $promotion->getBeginDate();
                $endDate = $promotion->getEndDate();
                if ($beginDate <= now() && $endDate >= now()) {
                    $validPromotions[] = $promotion;
                }
            }



            foreach ($validPromotions as $promotion) {
                $promotionName = $promotion->getPromotionName();
                $promotionDescription = $promotion->getPromotionDescription();
                $promotionBeginDate = $promotion->getBeginDate();
                $promotionEndDate = $promotion->getEndDate();
                $promotionReduction = $promotion->getReduction();
                $promotionImage = $promotion->getImagePromotion();

                $promotionData[] = [
                    'promotionName' => $promotionName,
                    'promotionDescription' => $promotionDescription,
                    'promotionBeginDate' => $promotionBeginDate->format('d-m-Y'),
                    'promotionEndDate' => $promotionEndDate->format('d-m-Y'),
                    'promotionReduction' => $promotionReduction,
                    'promotionImage' => $promotionImage,
                ];
            }
        }



        //PRODUCTS
        $products = $business->getProducts();

        $productData = [];
        foreach ($products as $product) {
            $productPhotosArray = [];
            $productPhotos = $product->getPhotos();
            foreach ($productPhotos as $productPhoto) {
                $productPhotosArray[] = $productPhoto;
            }
            $productFirstPhoto = $productPhotosArray[0];
            $productReviews = $product->getProductReviews();
            $productTotalScore = 0;
            $productCount = count($productReviews);

            if ($productCount > 0) {
                foreach ($productReviews as $review) {
                    $productTotalScore += $review->getScore();
                }
                $productAverageScore = $productTotalScore / $productCount;
            } else {
                $productAverageScore = null;
            }

            $productPrice = $product->getPrice();
            $productPromotionPrice = 0;
            if (count($validPromotions) > 0) {
                foreach ($validPromotions as $validPromotion) {
                    $promotion = ($productPrice / 100) * $validPromotion->getReduction();
                    $productPromotionPrice = $productPrice - $promotion;
                }
            }

            $productData[] = [
                'product' => $product,
                'productPrice' => $productPrice,
                'productPhoto' => $productFirstPhoto,
                'productPromotionPrice' => $productPromotionPrice,
                'productAverageScore' => round($productAverageScore),
            ];
        }


        // EVENTS
        $events = $business->getEvents();

        $currentEvents = [];
        foreach ($events as $event) {
            $eventBeginDate = $event->getBeginDate();
            $eventEndDate = $event->getEndDate();

            if ($eventBeginDate <= now() && $eventEndDate >= now()) {
                $currentEvents[] = $event;
            }
        }

        $currentEventsData = [];
        foreach ($currentEvents as $event) {
            $eventName = $event->getNameEvent();
            $eventDescription = $event->getDescriptionEvent();

            $currentEventsData[] = [
                'name' => $eventName,
                'description' => $eventDescription,
                'beginDate' => $eventBeginDate->format('d-m-Y'),
                'endDate' => $eventEndDate->format('d-m-Y'),
            ];
        }

        $today = strtolower(date("l"));
        $todayPlanning = [];

        foreach ($plannings as $planning) {
            $day = $planning->getDays()->getDayName();

            if (strtolower($day) == $today) {
                $todayPlanning = [
                    'openingHour' => $planning->getOpeningHour(),
                    'closingHour' => $planning->getClosingHour(),
                ];
            }
        }

        //STATISTICS
        $visitorCount = $business->getStatistic();
        if ($hasVisitedService->hasVisited($business->getId())) {
            $visitorCount++;
            $business->setStatistic($visitorCount);
            $entityManager->persist($business);
            $entityManager->flush();
        }

        $userConnected = $this->getUser();

        $userId = null;

        if ($userConnected) {
            $userId = $userConnected->getId();
        }

        return $this->render('business/business.html.twig', [
            'userId' => $userId,
            'business' => $business,
            'businessName' => $businessName,
            'businessCategory' => $businessCategory,
            'businessId' => $businessId,
            'businessAverageScore' => round($businessAverageScore),
            'businessPhotosArray' => $photoData,
            'first_photo' => $businessFirstPhoto,
            'promotionsData' => $promotionData,
            'productData' => $productData,
            'currentEventsData' => $currentEventsData,
            'reviewCount' => $reviewCount,
            'planningWeek' => $planningData,
            'todayPlanning' => $todayPlanning,
            'visitorCount' => $visitorCount,
        ]);
    }
}
