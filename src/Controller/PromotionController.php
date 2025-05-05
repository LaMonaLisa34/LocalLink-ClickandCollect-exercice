<?php

namespace App\Controller;

use App\Entity\Promotions;
use App\Repository\BusinessRepository;
use App\Repository\PromotionsRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class PromotionController extends AbstractController
{
    #[Route('/promotion', name: 'app_promotion')]
    public function index(BusinessRepository $businessRepository): Response
    {
        $businesses = $businessRepository->findAll();
        $validPromotions = [];

        foreach ($businesses as $business) {
            if ($business->isValidated()) {
                $promotions = $business->getPromotions();
                foreach ($promotions as $promotion) {
                    $validPromotions[] = $promotion;
                }
            }
        }

        $currentPromotions = [];
        $promotionsToCome = [];
        $previousPromotions = [];

        foreach ($validPromotions as $promotion) {

            $promotionBeginDate = $promotion->getBeginDate();
            $promotionEndDate = $promotion->getEndDate();

            $now = new DateTime();
            $now->setTime(23, 59, 59);

            $startOfDay = new DateTime();
            $startOfDay->setTime(0, 0, 0);

            if ($promotionBeginDate <= $now && $promotionEndDate >= $startOfDay) {
                $currentPromotions[] = $promotion;
            } else if ($promotionBeginDate > $now) {
                $promotionsToCome[] = $promotion;
            } else {
                $previousPromotions[] = $promotion;
            }
        }

        // dd($currentPromotions,$promotionsToCome);
        $currentPromotionsData = [];
        $promotionsToComeData = [];
        $previousPromotionsData = [];

        foreach ($currentPromotions as $promotion) {
            $businessId = $promotion->getBusiness()->getId();
            $businessName = $promotion->getBusiness()->getName();
            $busiNameFile = str_replace(' ','', $businessName);

            $promotionName = $promotion->getPromotionName();
            $promotionReduction = $promotion->getReduction();
            $promotionPhoto = $promotion->getImagePromotion();

            $promotionDescription = $promotion->getPromotionDescription();
            $promotionBeginDate = $promotion->getBeginDate()->format('d-m-Y');
            $promotionEndDate = $promotion->getEndDate()->format('d-m-Y');

            $currentPromotionsData[] = [
                'businessId' => $businessId,
                'busiNameFile' => $busiNameFile,
                'name' => $promotionName,
                'reduction' => $promotionReduction,
                'businessName' => $businessName,
                'promotionPhoto' => $promotionPhoto,
                'description' => $promotionDescription,
                'beginDate' => $promotionBeginDate,
                'endDate' => $promotionEndDate,
            ];
        }

        foreach ($promotionsToCome as $promotion) {
            $businessId = $promotion->getBusiness()->getId();
            $businessName = $promotion->getBusiness()->getName();
            $busiNameFile = str_replace(' ','', $businessName);

            $promotionPhoto = $promotion->getImagePromotion();

            $promotionName = $promotion->getPromotionName();
            $promotionReduction = $promotion->getReduction();

            $promotionDescription = $promotion->getPromotionDescription();
            $promotionBeginDate = $promotion->getBeginDate()->format('d-m-Y');
            $promotionEndDate = $promotion->getEndDate()->format('d-m-Y');

            $promotionsToComeData[] = [
                'businessId' => $businessId,
                'busiNameFile' => $busiNameFile,
                'name' => $promotionName,
                'reduction' => $promotionReduction,
                'businessName' => $businessName,
                'promotionPhoto' => $promotionPhoto,
                'description' => $promotionDescription,
                'beginDate' => $promotionBeginDate,
                'endDate' => $promotionEndDate,
            ];
        }

        foreach ($previousPromotions as $promotion) {
            $businessId = $promotion->getBusiness()->getId();
            $businessName = $promotion->getBusiness()->getName();
            $busiNameFile = str_replace(' ','', $businessName);

            $promotionPhoto = $promotion->getImagePromotion();

            $promotionName = $promotion->getPromotionName();
            $promotionReduction = $promotion->getReduction();

            $promotionDescription = $promotion->getPromotionDescription();
            $promotionBeginDate = $promotion->getBeginDate()->format('d-m-Y');
            $promotionEndDate = $promotion->getEndDate()->format('d-m-Y');

            $previousPromotionsData[] = [
                'businessId' => $businessId,
                'busiNameFile' => $busiNameFile,
                'name' => $promotionName,
                'reduction' => $promotionReduction,
                'businessName' => $businessName,
                'promotionPhoto' => $promotionPhoto,
                'description' => $promotionDescription,
                'beginDate' => $promotionBeginDate,
                'endDate' => $promotionEndDate,
            ];
        }
        // dd($currentPromotionsData,$promotionsToComeData,$previousPromotionsData);

        $userConnected = $this->getUser();
        $businessId = null;
        $userId = null;
        if ($userConnected) {

            $businesses = $userConnected->getBusiness();
            $userId = $userConnected->getId();
            foreach ($businesses as $business) {
                $businessId = $business->getId();
            }
        }

        return $this->render('promotion/index.html.twig', [
            'currentPromotionsData' => $currentPromotionsData,
            'promotionsToComeData' => $promotionsToComeData,
            'previousPromotionsData' => $previousPromotionsData,
            'businessId' => $businessId,
            'businessName' => $businessName,
            'userId' => $userId,
        ]);
    }


    #[Route('/business/{businessId}/myPromotions/', name: 'app_myPromotions')]
    public function myPromotions(string $businessId, BusinessRepository $businessRepository): Response
    {
        $userConnected = $this->getUser();

        $userId = null;

        if ($userConnected) {
            $userId = $userConnected->getId();
        
        } else {
            $this->addFlash('error', "Vous devez être connecté en tant que commercant pour accéder à la page 'mes promotions");
            return $this->redirectToRoute('app_homepage');
        }

        $myPromotions = $businessRepository->find($businessId)->getPromotions();
        $business = $businessRepository->find($businessId);

        $businessName = $business->getName();
        $busiNameFile = str_replace(' ', '', $businessName);


        $currentPromotions = [];
        $promotionsToCome = [];
        $previousPromotions = [];

        foreach ($myPromotions as $promotion) {


            $promotionBeginDate = $promotion->getBeginDate();
            $promotionEndDate = $promotion->getEndDate();

            $now = new DateTime();
            $now->setTime(23, 59, 59);

            $startOfDay = new DateTime();
            $startOfDay->setTime(0, 0, 0);

            if ($promotionBeginDate <= $now && $promotionEndDate >= $startOfDay) {
                $currentPromotions[] = $promotion;
            } else if ($promotionBeginDate > $now) {
                $promotionsToCome[] = $promotion;
            } else {
                $previousPromotions[] = $promotion;
            }
        }

        // dd($previousPromotions);
        return $this->render('promotion/myPromotions.html.twig', [
            'userId' => $userId,
            'businessId' => $businessId,
            'business' => $business,
            'busiNameFile' => $busiNameFile,
            'currentPromotions' => $currentPromotions,
            'promotionsToCome' => $promotionsToCome,
            'previousPromotions' => $previousPromotions,
        ]);
    }

    ########### CREATE ###########


    #[Route('/business/{businessId}/promotion/create', name: 'app_create_promotion')]
    public function createPromotion(string $businessId): Response
    {
        $userConnected = $this->getUser();
        $userId = null;
        if ($userConnected) {
            $userId = $userConnected->getId();
        } else {
            $this->addFlash('error', "Vous devez etre connectez en tant que commercant pour acceder à la page 'création de promotion");
            return $this->redirectToRoute('app_myPromotions');
        }

        return $this->render('promotion/createPromotion.html.twig', [
            'userId' => $userId,
            'businessId' => $businessId,
        ]);
    }

    #[Route('/business/{businessId}/promotion/created', name: 'app_promotion_created', methods: ['POST'])]
    public function createdPromotion(Request $request, string $businessId, EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager, BusinessRepository $businessRepository): Response
    {
        if ($request->isMethod('POST')) {
            $name = $request->get('name');
            $description = $request->get('description');
            $reduction = $request->get('reduction');
            $beginDate = $request->get('start_date');
            $beginDateFormat = new DateTime($beginDate);
            $endDate = $request->get('end_date');
            $endDateFormat = new DateTime($endDate);
            $photoAdded = $request->files;
   
            $token = $request->request->get('_token');
            if (!$csrfTokenManager->isTokenValid(new CsrfToken('create_promotion', $token))) {
                $this->addFlash('error', "Échec de création de la promotion, jeton CSRF invalide");
                return $this->redirectToRoute('app_myPromotions', ['businessId' => $businessId]);
            }
   
            $userConnected = $this->getUser();
            if ($userConnected) {
                $promotion = new Promotions();
                $promotion->setPromotionName($name);
                $promotion->setPromotionDescription($description);
                $promotion->setReduction($reduction);
                $promotion->setBeginDate($beginDateFormat);
                $promotion->setEndDate($endDateFormat);
   
                $business = $businessRepository->find($businessId);
                $businessName = $business->getName();
                $busiNameFile = str_replace(' ', '', $businessName);
   
                if (is_null($photoAdded->get('photo'))) {
                    $this->addFlash('error', 'Vous devez ajouter au moins une photo.');
                    return $this->redirectToRoute('app_create_promotion', ['businessId' => $businessId]);
                } else {
                    foreach ($photoAdded as $fileName) {
                        if ($fileName) {
                            $photoName = uniqid() . "." . $fileName->guessExtension();
                            $uploadsDir = $this->getParameter('uploadsDirectory') . "/" . $busiNameFile . "/promotionPhoto";
   
                            // Vérification et création du dossier si nécessaire
                            if (!is_dir($uploadsDir)) {
                                mkdir($uploadsDir, 0775, true);
                            }
   
                            // Déplacement du fichier dans le dossier correspondant
                            $fileName->move($uploadsDir, $photoName);
                        }
                    }
                }
   
                $promotion->setImagePromotion($photoName);
                $business->addPromotion($promotion);
                $entityManager->persist($promotion);
                $entityManager->flush();
   
                $this->addFlash('success', "La promotion a été ajoutée avec succès.");
            }
        }
   
        return $this->redirectToRoute('app_myPromotions', ['businessId' => $businessId]);
    }

    ########### UPDATE ###########

    #[Route('/business/{businessId}/promotion/{promotionId}/update', name: 'app_update_promotion')]
    public function updatePromotion(string $promotionId, string $businessId, BusinessRepository $businessRepository, PromotionsRepository $promotionsRepository): Response
    {
        $userConnected = $this->getUser();


        if ($userConnected) {
            $business = $businessRepository->find($businessId);
            $userId = $userConnected->getId();
            
        } else {
            $this->addFlash('error', "Vous devez etre connectez en tant que commercant pour acceder à la page 'édition de promotion");
            return $this->redirectToRoute('app_homepage');
        }

        $promotion = $promotionsRepository->find($promotionId);
        return $this->render('promotion/updatePromotions.html.twig', [
            'userId' => $userId,
            'business' => $business,
            'busiNameFile' => str_replace(' ','',$business->getName()),
            'businessId' => $businessId,
            'promotion' => $promotion,
            'beginDate' => $promotion->getBeginDate()->format('Y-m-d'),
            'endDate' => $promotion->getEndDate()->Format('Y-m-d'),
        ]);
    }

    #[Route('/business/{businessId}/promotion/{promotionId}/updated', name: 'app_promotion_updated', methods: ['POST'])]
    public function updatedPromotion(Request $request, string $businessId, string $promotionId, EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager, BusinessRepository $businessRepository, PromotionsRepository $promotionsRepository): Response
    {
        if ($request->isMethod('POST')) {


            $name = $request->get('name');
            $description = $request->get('description');
            $beginDate = $request->get('start_date');
            $beginDateFormat = new DateTime($beginDate);
            $endDate = $request->get('end_date');
            $endDateFormat = new DateTime($endDate);

            $userConnected = $this->getUser();
            $business = $businessRepository->find($businessId);

            $businessName = $request->request->get('name');
            $busiNameFile = str_replace(' ', '', $businessName);

            $promotion = $promotionsRepository->find($promotionId);
            if ($userConnected) {

                $userId = $userConnected->getId();

                $token = $request->request->get('_token');
                if (!$csrfTokenManager->isTokenValid(new CsrfToken('update_promotion', $token))) {
                    $this->addFlash('error', "Echec de l'édition de la promotion, jeton CSRF invalide");
                    return $this->redirectToRoute('app_myPromotions', ['businessId' => $businessId, 'userId' => $userId]);
                }

                $photoAdded = $request->files; 
                $promotionPhoto = $promotion->getImagePromotion(); 
                $getAll = $request->request->all();
                $photosRequest = $request->get('photo') ?? null;
                if (!$photosRequest && is_null($photoAdded->get('photo'))) {
                    $this->addFlash('error', 'Vous devez conserver une photo.');
                    return $this->redirectToRoute('app_update_promotion', ['businessId' => $businessId, 'promotionId' => $promotionId]);
                } elseif ($photosRequest && !is_null($photoAdded->get('photo'))) {
                    $this->addFlash('error', 'Vous devez conserver qu\'une seule photo.');
                    return $this->redirectToRoute('app_update_promotion', ['businessId' => $businessId, 'promotionId' => $promotionId]);
                } else {

                    if ($photosRequest !== $promotionPhoto) {
                        $filePath = $this->getParameter('uploadsDirectory') . '/' . $busiNameFile . '/promotionPhoto/' . $promotionPhoto;

                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }

                    }


                    $photoFile = $request->files->get('photo');
                    if ($photoFile) {
                        $photoName = $photoFile->getFilename() . "." . $photoFile->guessExtension();

                        $photoFile->move(
                            $this->getParameter('uploadsDirectory') . '/' . $busiNameFile . '/promotionPhoto',
                            $photoName
                        );

                        $promotion->setImagePromotion($photoName);
                        $entityManager->persist($promotion);
                    }
                }

                $promotion = $promotionsRepository->find($promotionId);
                $promotion->setPromotionName($name);
                $promotion->setPromotionDescription($description);
                $promotion->setBeginDate($beginDateFormat);
                $promotion->setEndDate($endDateFormat);
                $entityManager->persist($promotion);
                $entityManager->flush();

                $this->addFlash('success', "La promotion a été éditée avec succès.");
            }
        }
        if (!$request->isMethod('POST')) {
            $this->addFlash('error', "L'édition de la promotion a échoué");
            return $this->redirectToRoute('app_myPromotions', ['businessId' => $businessId, 'userId' => $userId]);
        }

        return $this->redirectToRoute('app_myPromotions', ['businessId' => $businessId, 'userId' => $userId]);
    }


    ########### DELETE ###########

    #[Route('/business/{businessId}/promotion/{promotionId}/delete', name: 'app_delete_promotion')]
    public function deletePromotion(string $promotionId, string $businessId, Request $request, PromotionsRepository $promotionsRepository, EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        if ($request->isMethod('POST')) {

            $userConnected = $this->getUser();
            if ($userConnected) {

                $businesses = $userConnected->getBusiness();
                $userId = $userConnected->getId();

                $token = $request->request->get('_token');
                if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete_promotion', $token))) {
                    $this->addFlash('error', "Echec de la suppression de la promotion, jeton CSRF invalide");
                    return $this->redirectToRoute('app_myPromotions', ['businessId' => $businessId, 'userId' => $userId]);
                }

                foreach ($businesses as $businessRef) {
                    $businessId = $businessRef->getId();
                }

                $promotion = $promotionsRepository->find($promotionId);
                $entityManager->remove($promotion);
                $entityManager->flush();

                $this->addFlash('success', "La promotion a été supprimée avec succès.");
            }
        }
        if (!$request->isMethod('POST')) {
            $this->addFlash('error', "La suppression de la promotion a échoué");
            return $this->redirectToRoute('app_myPromotions', ['businessId' => $businessId, 'userId' => $userId]);
        }

        return $this->redirectToRoute('app_myPromotions', ['businessId' => $businessId, 'userId' => $userId]);
    }
}
