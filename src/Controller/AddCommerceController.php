<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Business;
use App\Entity\Days;
use App\Entity\Planning;
use App\Entity\BusinessPhotos;
use App\Form\AddCommerceType;

class AddCommerceController extends AbstractController
{
    #[Route('/add_commerce', name: 'add_commerce')]
    public function addCommerce(Request $request, EntityManagerInterface $entityManager, ?int $id = null): Response
    {
        $userConnected = $this->getUser();


        if ($id) {
            // Charger un commerce existant
            $business = $entityManager->getRepository(Business::class)->find($id);
            if (!$business) {
                throw $this->createNotFoundException("Le commerce avec l'ID $id n'existe pas.");
            }
        } else {
            $business = new Business();
            // Créer un nouveau commerce
        }

        $form = $this->createForm(AddCommerceType::class, $business);
        $form->handleRequest($request);
        $getall = $request->request->all();

        if ($form->isSubmitted() && $form->isValid()) {

            $business->setOwner($userConnected);
            // Valeur vide pour test enregistrement bdd
            if (!$business->getStatistic()) {
                $business->setStatistic(0);
            }

            if (!$business->getFootprintCarbon()) {
                $business->setFootprintCarbon(0.0);
            }

            if (!$business->getCategories()) {
                // Ajoutez une catégorie par défaut si nécessaire
                $business->setCategories(null);
            }

            if (!$business->isValidated()) {
                // Ajoutez une catégorie par défaut si nécessaire
                $business->setValidated(0);
            }

            $entityManager->persist($business);

            // Gestion des jours d'ouverture
            $daysRepository = $entityManager->getRepository(Days::class);
            $allDays = $daysRepository->findAll();
            // dd($allDays);

            foreach ($allDays as $day) {
                $dayName = strtolower($day->getDayName());
                $openingHour = $getall['add_commerce'][$dayName . '_opening'];
                $closingHour = $getall['add_commerce'][$dayName . '_closing'];
                // dd($openingHour,$closingHour);

                $validHourPattern = '/^(?:[01]\d|2[0-3]):[0-5]\d$/';

                if ($openingHour !== 'fermé' && !preg_match($validHourPattern, $openingHour)) {
                    $this->addFlash('error', "Le format de l'heure d'ouverture pour {$day->getDayName()} est invalide.");
                    return $this->redirectToRoute('add_commerce', ['id' => $business->getId()]);
                }

                if ($closingHour !== 'fermé' && !preg_match($validHourPattern, $closingHour)) {
                    $this->addFlash('error', "Le format de l'heure de fermeture pour {$day->getDayName()} est invalide.");
                    return $this->redirectToRoute('add_commerce', ['id' => $business->getId()]);
                }

                $planning = new Planning();

                $planning->setOpeningHour($openingHour);
                $planning->setClosingHour($closingHour);

                $planning->setDays($day);

                $entityManager->persist($planning);
                $business->addPlanning($planning);
            }
            // dd($business->getPlannings());

            // Gestion des photos uploadées
            $photoFields = ['photo1', 'photo2', 'photo3', 'photo4'];
            foreach ($photoFields as $fieldName) {

                /** @var UploadedFile $photo */
                $photo = $form->get($fieldName)->getData();
                if ($photo) {
                    // Générer un nom unique pour chaque fichier
                    $fileName = uniqid() . '.' . $photo->guessExtension();

                    // Déplacer le fichier dans le répertoire des uploads
                    $photo->move(
                        $this->getParameter('uploads_directory'),
                        $fileName
                    );

                    // Créer une nouvelle entité BusinessPhotos
                    $businessPhoto = new BusinessPhotos();
                    $businessPhoto->setPhoto($fileName);
                    $businessPhoto->setBusiness($business);

                    $entityManager->persist($businessPhoto);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Commerce ajouté avec succès !');
            return $this->redirectToRoute('add_commerce', ['id' => $business->getId()]);
        }

        // Passer les photos existantes à la vue
        $photos = $business->getBusinessPhotos();

        return $this->render('add_commerce/index.html.twig', [
            'form' => $form->createView(),
            'photos' => $photos,
        ]);
    }
}
