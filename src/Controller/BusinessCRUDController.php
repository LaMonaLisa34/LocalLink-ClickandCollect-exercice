<?php

namespace App\Controller;

use App\Entity\Business;
use App\Entity\BusinessPhotos;
use App\Repository\BusinessRepository;
use App\Repository\CategoriesRepository;
use App\Repository\PlanningRepository;
use App\Service\GeocodingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/business/c/r/u/d')]
class BusinessCRUDController extends AbstractController
{

    #[Route('/business/edit/{id}', name: 'app_business_c_r_u_d_edit', methods: ['GET', 'POST'])]
    public function edit(BusinessRepository $businessRepository, CategoriesRepository $categoriesRepository, string $id): Response
    {
        $business = $businessRepository->find($id);
        $businessName = $businessRepository->find($id)->getName();
        $busiNameFile = str_replace(' ', '', $businessName);
        // dd($busiNameFile);

        $planning = $businessRepository->find($id)->getPlannings();
        $categories = $categoriesRepository->findAll();
        $photos = $business->getBusinessPhotos();

        // récupérer les catégories de la bdd
        $categoriesArray = [];

        foreach ($categories as $category) {
            $categoryName = $category->getCategory('category');
            $categoriesArray[] = $categoryName;
        }

        // récupérer chaque planning du business
        $j = 0;
        $planningWeek = [];
        $dayNameFrench = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        foreach ($planning as $day) {
            $day = $planning[$j]->getDays()->getDayName();
            $openingHour = $planning[$j]->getOpeningHour();
            $closingHour = $planning[$j]->getClosingHour();
            $planningWeek[] = [
                "dayFrench" => $dayNameFrench[$j],
                "day" => $day,
                "opening" => $openingHour,
                "closing" => $closingHour,
            ];
            $openingHour = "";
            $closingHour = "";
            $j++;
            $day = "";
        }

        $userConnected = $this->getUser();
        $userId = null;
        if ($userConnected) {

            $userId = $userConnected->getId();
        }

        return $this->render(
            'business_crud/edit.html.twig',
            [
                'planningWeek' => $planningWeek,
                'business' => $business,
                'busiNameFile' => $busiNameFile,
                'categories' => $categoriesArray,
                'photos' => $photos,
                'businessName' => $businessName,
                'businessId' => $id,
                'userId' => $userId
            ]
        );
    }

    #[Route('/business/editbusiness/updated', name: 'business.updated')]
    public function updated(GeocodingService $geocodingService, EntityManagerInterface $entityManager, BusinessRepository $businessRepository, PlanningRepository $planningRepository, Request $request, CsrfTokenManagerInterface $csrfTokenManager): Response
    {

        $csrfToken = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('business.updated', $csrfToken))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }
        $businessUpdated = $request->request->all();
        $idBusiness = $request->request->get('id');

        $oldBusiness = $businessRepository->find($idBusiness);
        $oldBusinessName = $oldBusiness->getName();
        $oldBusinessNameFile = str_replace(' ', '', $oldBusinessName);
        $businessName = $request->request->get('name');
        $busiNameFile = str_replace(' ', '', $businessName);
        $filePath = $this->getParameter('uploadsDirectory') . "/" . $busiNameFile;
        $filePathPromoPhoto = $this->getParameter('uploadsDirectory') . "/" . $busiNameFile . "/promotionPhoto";
        $filePathProductsPhotos = $this->getParameter('uploadsDirectory') . "/" . $busiNameFile . "/products";
        $productsBusiness = $oldBusiness->getProducts();
        $oldFilePathProductsPhotos = $this->getParameter('uploadsDirectory') . "/" . $oldBusinessNameFile . "/products";
        $promoBusiness = $oldBusiness->getPromotions();
        $oldFilePathPromoPhotos = $this->getParameter('uploadsDirectory') . "/" . $oldBusinessNameFile . "/promotionPhoto";
        if (!is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }
        if (is_dir($oldFilePathProductsPhotos)) {
            mkdir($filePathProductsPhotos, 0777, true);
        }
        if (is_dir($oldFilePathPromoPhotos)) {
            mkdir($filePathPromoPhoto, 0777, true);
        }

        if ($busiNameFile != $oldBusinessNameFile) {
            $photos = $oldBusiness->getBusinessPhotos();
            foreach ($photos as $photo) {
                $photoName = $photo->getPhoto();
                copy(
                    $this->getParameter('uploadsDirectory') . "/" . $oldBusinessNameFile . '/' . $photoName,
                    $filePath . '/' . $photoName
                );
                unlink(
                    $this->getParameter('uploadsDirectory') . "/" . $oldBusinessNameFile . '/' . $photoName
                );
            }


            if (is_dir($oldFilePathProductsPhotos)) {
                foreach ($productsBusiness as $product) {
                    $photos = $product->getPhotos();
                    foreach ($photos as $photo) {
                        $photoName = $photo->getPhoto();
                        copy(
                            $this->getParameter('uploadsDirectory') . "/" . $oldBusinessNameFile . '/products/' . $photoName,
                            $filePathProductsPhotos . '/' . $photoName
                        );
                        unlink(
                            $this->getParameter('uploadsDirectory') . "/" . $oldBusinessNameFile . '/products/' . $photoName
                        );
                    }
                }
                rmdir(
                    $this->getParameter('uploadsDirectory') . "/" . $oldBusinessNameFile . '/products/'
                );
            }

            if (is_dir($oldFilePathPromoPhotos)) {

                foreach ($promoBusiness as $promo) {
                    $photoName = $promo->getImagePromotion();
                    // dd($photoName);

                    copy(
                        $this->getParameter('uploadsDirectory') . "/" . $oldBusinessNameFile . '/promotionPhoto/' . $photoName,
                        $filePathPromoPhoto . '/' . $photoName
                    );
                    unlink(
                        $this->getParameter('uploadsDirectory') . "/" . $oldBusinessNameFile . '/promotionPhoto/' . $photoName
                    );
                }
                rmdir(
                    $this->getParameter('uploadsDirectory') . "/" . $oldBusinessNameFile . '/promotionPhoto/'
                );
            }
            rmdir(
                $this->getParameter('uploadsDirectory') . "/" . $oldBusinessNameFile
            );
        }



        $photo1 = $request->request->get('photo1');
        $photo2 = $request->request->get('photo2');
        $photo3 = $request->request->get('photo3');
        $photo4 = $request->request->get('photo4');
        $business = $businessRepository->find($idBusiness);
        $businessPhotos = $businessRepository->find($idBusiness)->getBusinessPhotos();
        $planningUpdated = $businessRepository->find($idBusiness)->getPlannings();
        $arrayPhotosAdd = [$photo1, $photo2, $photo3, $photo4];
        $photoFields = ['photo1', 'photo2', 'photo3', 'photo4'];


        $photosRequest = $businessUpdated['photos'] ?? [];
        // dd($photosRequest);
        if (count($photosRequest) === 0 && count($arrayPhotosAdd) === 0) {
            $this->addFlash('error', 'Vous devez conserver au moins une photo.');
            return $this->redirectToRoute('app_business_c_r_u_d_edit', ['businessId' => $idBusiness]);
        } else {

            foreach ($businessPhotos as $photo) {
                $idPhoto = $photo->getId();
                if (!in_array($idPhoto, $photosRequest)) {
                    $filePath = $this->getParameter('uploadsDirectory') . '/' . $busiNameFile . '/' . $photo->getPhoto();

                    // Supprimer le fichier du dossier uploads
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }

                    // supprime les photos de la bdd
                    $business->removeBusinessPhoto($photo);
                    $entityManager->remove($photo);
                    $entityManager->persist($business);
                }
            }


            foreach ($photoFields as $fileName) {
                $photoAdded = $request->files->get($fileName);
                if ($photoAdded) {
                    $photoName = $photoAdded->getFilename() . "." . $photoAdded->guessExtension();

                    // Déplacer le fichier dans le répertoire des uploads/businessNameId
                    $photoAdded->move(
                        $this->getParameter('uploadsDirectory') . "/" . $busiNameFile,
                        $photoName
                    );

                    // Créer une nouvelle entité BusinessPhotos
                    $photo = new BusinessPhotos();
                    $photo->setPhoto($photoName);
                    $photo->setBusiness($business);
                    $entityManager->persist($photo);
                }
            }
        }

        // remet les raisons du rejet de publication du business à null
        $business->setRejectionReason(NULL);
        $business->setValidated(0);

        $entityManager->persist($business);

        // envoie les coordonnées GPS dans la bdd en fonction de l'adresse 
        $streetNumber = $request->request->get('streetNumber');
        $streetName = $request->request->get('streetName');
        $cityName = $request->request->get('CityName');
        $address = "$streetNumber " . "$streetName, " . "$cityName, " . "France";
        $coordinates = $geocodingService->getCoordinates($address);
        // dd($coordinates);
        $business->setLat($coordinates['lat']);
        $business->setLon($coordinates['lon']);

        // update les plannings
        $j = 0;
        foreach ($planningUpdated as $day) {
            $day = $planningUpdated[$j]->getDays()->getDayName();
            $planningID = $planningUpdated[$j]->getId();
            $hours = [];
            $openingHour = $businessUpdated[$day]['opening'];
            $closingHour = $businessUpdated[$day]['closing'];
            $hours = [
                'id' => $planningID,
                'opening_hour' => $openingHour,
                'closing_hour' => $closingHour,
            ];
            $planningRepository->updateById($hours);
            $j++;
            $day = "";
        }
        $userConnected = $this->getUser();
        $userId = null;
        if ($userConnected) {

            $userId = $userConnected->getId();
        }

        $businessRepository->updateById($businessUpdated);
        $entityManager->flush();

        return $this->redirectToRoute('app_gestionnaire_compte', [
            'userId' => $userId
        ]);
    }



    #[Route('/business/delete/{id}', name: 'app_business_c_r_u_d_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        Business $business,
        EntityManagerInterface $entityManager,
        BusinessRepository $businessRepository,
        string $id,
        Security $security,
    ): Response {
        // User connecté
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException("Vous devez être connecté pour supprimer un commerce.");
        }

        // Recuperer id commerce
        $business = $businessRepository->find($id);
        if (!$business) {
            throw $this->createNotFoundException("Le commerce que vous tentez de supprimer n'existe pas.");
        }

        // Verification que le user est le owner
        if ($business->getOwner() !== $user) {
            throw $this->createAccessDeniedException("Vous n'avez pas le droit de supprimer ce commerce.");
        }

        // CSRF pour la sécurité
        if (!$this->isCsrfTokenValid('delete_business', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException("Token CSRF invalide.");
        }

        // Suppression des planning associés au business
        foreach ($business->getPlannings() as $planning) {
            $business->removePlanning($planning);
        }
        $businessName = str_replace(' ', '', $business->getName());
        $photos = $business->getBusinessPhotos();
        foreach ($photos as $photo) {
            $photoName = $photo->getPhoto();

            unlink(
                $this->getParameter('uploadsDirectory') . "/" . $businessName . '/' . $photoName
            );
        }
        $productsBusiness = $business->getProducts();
        $filePathProductsPhotos = $this->getParameter('uploadsDirectory') . "/" . $businessName . "/products";
        if (is_dir($filePathProductsPhotos)) {
            foreach ($productsBusiness as $product) {
                $photos = $product->getPhotos();
                foreach ($photos as $photo) {
                    $photoName = $photo->getPhoto();

                    unlink(
                        $this->getParameter('uploadsDirectory') . "/" . $businessName . '/products/' . $photoName
                    );
                }
            }
            rmdir(
                $this->getParameter('uploadsDirectory') . "/" . $businessName . '/products/'
            );
        }

        $promoBusiness = $business->getPromotions();
        $filePathPromoPhoto = $this->getParameter('uploadsDirectory') . "/" . $businessName . "/promotionPhoto";
        if (is_dir($filePathPromoPhoto)) {

            foreach ($promoBusiness as $promo) {
                $photoName = $promo->getImagePromotion();
                unlink(
                    $this->getParameter('uploadsDirectory') . "/" . $businessName . '/promotionPhoto/' . $photoName
                );
            }
            rmdir(
                $this->getParameter('uploadsDirectory') . "/" . $businessName . '/promotionPhoto/'
            );
        }
        rmdir(
            $this->getParameter('uploadsDirectory') . "/" . $businessName
        );

        // MAJ du role -> passage en user Et ADMIN reste admin
        $userConnected = $this->getUser();

        $currentRoles = $userConnected->getRoles();

        if (!in_array('ROLE_ADMIN', $currentRoles)) {
            $currentRoles = array_filter($currentRoles, function ($role) {
                return $role !== 'ROLE_BUSI';
            });

            // $currentRoles[] = '';
            $currentRoles[] = 'ROLE_USER';
        }
        // }
        $userConnected->setRoles($currentRoles);
        $entityManager->persist($userConnected);

        // delete business
        $entityManager->remove($business);
        $entityManager->flush();

        // Forcer a rester connecter après la suppression du commerce -> rafraichir le token
        $security->login($user);

        // Flash 
        $this->addFlash('success', 'Votre commerce a été supprimé avec succès.');

        return $this->redirectToRoute('app_gestionnaire_compte', ['userId' => $user->getId()], Response::HTTP_SEE_OTHER);
    }
}
