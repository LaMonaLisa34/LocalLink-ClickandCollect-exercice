<?php

namespace App\Controller;

use DateTime;
use App\Entity\Events;
use App\Repository\BusinessRepository;
use App\Repository\EventsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;


class EventController extends AbstractController
{

    ########### READ ###########

    #[Route('/event', name: 'app_event')]
    public function index(BusinessRepository $businessRepository): Response
    {
        $businesses = $businessRepository->findAll();
        $validEvents = [];

        foreach ($businesses as $business) {
            if ($business->isValidated()) {
                $events = $business->getEvents();
                foreach ($events as $event) {
                    $validEvents[] = $event;
                }
            }
        }

        $currentEvents = [];
        $eventsToCome = [];
        $previousEvents = [];

        foreach ($validEvents as $event) {

            $eventBeginDate = $event->getBeginDate();
            $eventEndDate = $event->getEndDate();

            $now = new DateTime();
            $now->setTime(23, 59, 59);

            $startOfDay = new DateTime();
            $startOfDay->setTime(0, 0, 0);

            if ($eventBeginDate <= $now && $eventEndDate >= $startOfDay) {
                $currentEvents[] = $event;
            } else if ($eventBeginDate > $now) {
                $eventsToCome[] = $event;
            } else {
                $previousEvents[] = $event;
            }
        }

        // dd($currentEvents,$eventsToCome);
        $currentEventsData = [];
        $eventsToComeData = [];
        $previousEventsData = [];

        foreach ($currentEvents as $event) {
            $businessId = $event->getBusiness()->getId();
            $eventName = $event->getNameEvent();
            $eventDescription = $event->getDescriptionEvent();
            $eventBeginDate = $event->getBeginDate()->format('d-m-Y');
            $eventEndDate = $event->getEndDate()->format('d-m-Y');

            $currentEventsData[] = [
                'businessId' => $businessId,
                'name' => $eventName,
                'description' => $eventDescription,
                'beginDate' => $eventBeginDate,
                'endDate' => $eventEndDate,
            ];
        }

        foreach ($eventsToCome as $event) {
            $businessId = $event->getBusiness()->getId();
            $eventName = $event->getNameEvent();
            $eventDescription = $event->getDescriptionEvent();
            $eventBeginDate = $event->getBeginDate()->format('d-m-Y');
            $eventEndDate = $event->getEndDate()->format('d-m-Y');

            $eventsToComeData[] = [
                'businessId' => $businessId,
                'name' => $eventName,
                'description' => $eventDescription,
                'beginDate' => $eventBeginDate,
                'endDate' => $eventEndDate,
            ];
        }

        foreach ($previousEvents as $event) {
            $businessId = $event->getBusiness()->getId();
            $eventName = $event->getNameEvent();
            $eventDescription = $event->getDescriptionEvent();
            $eventBeginDate = $event->getBeginDate()->format('d-m-Y');
            $eventEndDate = $event->getEndDate()->format('d-m-Y');

            $previousEventsData[] = [
                'businessId' => $businessId,
                'name' => $eventName,
                'description' => $eventDescription,
                'beginDate' => $eventBeginDate,
                'endDate' => $eventEndDate,
            ];
        }

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

        return $this->render('event/event.html.twig', [
            'currentEventsData' => $currentEventsData,
            'eventsToComeData' => $eventsToComeData,
            'previousEventsData' => $previousEventsData,
            'businessId' => $businessId,
            'userId' => $userId,
        ]);
    }


    #[Route('/business/{businessId}/myEvents/', name: 'app_myEvent')]
    public function myEvents(string $businessId, BusinessRepository $businessRepository): Response
    {
        $userConnected = $this->getUser();

        $businessId = null;
        $userId = null;

        if ($userConnected) {
            $businesses = $userConnected->getBusiness();
            $userId = $userConnected->getId();
            foreach ($businesses as $business) {
                $businessId = $business->getId();
            }
        } else {
            $this->addFlash('error', "Vous devez être connecté en tant que commercant pour accéder à la page 'mes évènement'");
            return $this->redirectToRoute('app_homepage');
        }

        $business = $businessRepository->find($businessId);
        $myEvents = $business->getEvents();

        $currentEvents = [];
        $eventsToCome = [];
        $previousEvents = [];

        foreach ($myEvents as $event) {

            $eventBeginDate = $event->getBeginDate();
            $eventEndDate = $event->getEndDate();

            $now = new DateTime();
            $now->setTime(23, 59, 59);

            $startOfDay = new DateTime();
            $startOfDay->setTime(0, 0, 0);

            if ($eventBeginDate <= $now && $eventEndDate >= $startOfDay) {
                $currentEvents[] = $event;
            } else if ($eventBeginDate > $now) {
                $eventsToCome[] = $event;
            } else {
                $previousEvents[] = $event;
            }
        }

        return $this->render('event/myEvent.html.twig', [
            'userId' => $userId,
            'business' => $business,
            'businessId' => $businessId,
            'currentEvents' => $currentEvents,
            'eventsToCome' => $eventsToCome,
            'previousEvents' => $previousEvents,
        ]);
    }

    ########### CREATE ###########


    #[Route('/business/{businessId}/event/create', name: 'app_create_event')]
    public function createEvent(string $businessId): Response
    {
        $userConnected = $this->getUser();
        $userId = null;
        if ($userConnected) {
            $userId = $userConnected->getId();
        } else {
            $this->addFlash('error', "Vous devez être connecté en tant que commercant pour accéder à la page 'création d'évènement'");
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('event/createEvent.html.twig', [
            'userId' => $userId,
            'businessId' => $businessId,
        ]);
    }

    #[Route('/business/{businessId}/event/created', name: 'app_event_created', methods: ['POST'])]
    public function createdEvent(Request $request, string $businessId, EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        if ($request->isMethod('POST')) {


            $name = $request->get('name');
            $description = $request->get('description');
            $beginDate = $request->get('start_date');
            $beginDateFormat = new DateTime($beginDate);
            $endDate = $request->get('end_date');
            $endDateFormat = new DateTime($endDate);
            // dd($beginDateFormat);

            $userConnected = $this->getUser();

            if ($userConnected) {
                $event = new Events();
                $event->setNameEvent($name);
                $event->setDescriptionEvent($description);
                $event->setBeginDate($beginDateFormat);
                $event->setEndDate($endDateFormat);
                // $business = $businessRepository->find($businessId);

                $businesses = $userConnected->getBusiness();
                $userId = $userConnected->getId();

                $token = $request->request->get('_token');
                if (!$csrfTokenManager->isTokenValid(new CsrfToken('create_event', $token))) {
                    $this->addFlash('error', "Echec de création d'évènement, jeton CSRF invalide");
                    return $this->redirectToRoute('app_myEvent', ['businessId' => $businessId, 'userId' => $userId]);
                    // throw $this->createAccessDeniedException('Token CSRF invalide.');
                }

                foreach ($businesses as $businessRef) {
                    $business = $businessRef;

                    $business->addEvent($event);
                    $entityManager->persist($event);
                    $entityManager->flush();

                    $businessId = $businessRef->getId();
                }

                $this->addFlash('success', "L'évènement a été ajouté avec succès.");
            }
        }
        if (!$request->isMethod('POST')) {
            $this->addFlash('error', "La création de l'évènement a échoué");
            return $this->redirectToRoute('app_myEvent', ['businessId' => $businessId, 'userId' => $userId]);
        }

        return $this->redirectToRoute('app_myEvent', ['businessId' => $businessId, 'userId' => $userId]);
    }

    ########### UPDATE ###########

    #[Route('/business/event/{eventId}/update', name: 'app_update_event')]
    public function updateEvent(string $eventId, EventsRepository $eventsRepository): Response
    {
        $userConnected = $this->getUser();

        $businessId = null;
        $userId = null;

        if ($userConnected) {
            $businesses = $userConnected->getBusiness();
            $userId = $userConnected->getId();
            foreach ($businesses as $business) {
                $businessId = $business->getId();
            }
        } else {
            $this->addFlash('error', "Vous devez être connecté en tant que commercant pour accéder à la page 'modification d'évènement'");
            return $this->redirectToRoute('app_homepage');
        }

        $event = $eventsRepository->find($eventId);
        return $this->render('event/updateEvent.html.twig', [
            'userId' => $userId,
            'businessId' => $businessId,
            'event' => $event,
            'beginDate' => $event->getBeginDate()->format('Y-m-d'),
            'endDate' => $event->getEndDate()->Format('Y-m-d'),
        ]);
    }

    #[Route('/business/{businessId}/event/{eventId}/updated', name: 'app_event_updated', methods: ['POST'])]
    public function updatedEvent(Request $request, string $businessId, string $eventId, EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager, EventsRepository $eventsRepository): Response
    {
        if ($request->isMethod('POST')) {


            $name = $request->get('name');
            $description = $request->get('description');
            $beginDate = $request->get('start_date');
            $beginDateFormat = new DateTime($beginDate);
            $endDate = $request->get('end_date');
            $endDateFormat = new DateTime($endDate);

            $userId = null;
            $userConnected = $this->getUser();
            if ($userConnected) {

                $businesses = $userConnected->getBusiness();
                $userId = $userConnected->getId();

                $token = $request->request->get('_token');
                if (!$csrfTokenManager->isTokenValid(new CsrfToken('update_event', $token))) {
                    $this->addFlash('error', "Echec de la modification d'évènement, jeton CSRF invalide");
                    return $this->redirectToRoute('app_myEvent', ['businessId' => $businessId, 'userId' => $userId]);
                }

                foreach ($businesses as $businessRef) {
                    $businessId = $businessRef->getId();
                }

                $event = $eventsRepository->find($eventId);
                $event->setNameEvent($name);
                $event->setDescriptionEvent($description);
                $event->setBeginDate($beginDateFormat);
                $event->setEndDate($endDateFormat);
                $entityManager->persist($event);
                $entityManager->flush();

                $this->addFlash('success', "L'évènement a été modifié avec succès.");
            }
        }
        if (!$request->isMethod('POST')) {
            $this->addFlash('error', "La modification de l'évènement a échoué");
            return $this->redirectToRoute('app_myEvent', ['businessId' => $businessId, 'userId' => $userId]);
        }

        return $this->redirectToRoute('app_myEvent', ['businessId' => $businessId, 'userId' => $userId]);
    }


    ########### DELETE ###########

    #[Route('/business/{businessId}/event/{eventId}/delete', name: 'app_delete_event')]
    public function deleteEvent(string $eventId, string $businessId, Request $request, EventsRepository $eventsRepository, EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        if ($request->isMethod('POST')) {

            $userConnected = $this->getUser();
            if ($userConnected) {

                $businesses = $userConnected->getBusiness();
                $userId = $userConnected->getId();

                $token = $request->request->get('_token');
                if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete_event', $token))) {
                    $this->addFlash('error', "Echec de la suppression de l'évènement, jeton CSRF invalide");
                    return $this->redirectToRoute('app_myEvent', ['businessId' => $businessId, 'userId' => $userId]);
                }

                foreach ($businesses as $businessRef) {
                    $businessId = $businessRef->getId();
                }

                $event = $eventsRepository->find($eventId);
                $entityManager->remove($event);
                $entityManager->flush();

                $this->addFlash('success', "L'évènement a été supprimé avec succès.");
            }
        }
        if (!$request->isMethod('POST')) {
            $this->addFlash('error', "La suppression de l'évènement a échoué");
            return $this->redirectToRoute('app_myEvent', ['businessId' => $businessId, 'userId' => $userId]);
        }

        return $this->redirectToRoute('app_myEvent', ['businessId' => $businessId, 'userId' => $userId]);
    }
}
