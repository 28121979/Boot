<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Invoice;
use App\Entity\Product;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
class BookingController extends AbstractController
{
    #[Route('/bookings', name: 'bookings')] 
    public function bookings(BookingRepository $bookingRepository): JsonResponse 
    {
        $bookings = $bookingRepository->findAll(); // Récupère tous les enregistrements de la table Booking
        $events = []; // Tableau pour stocker les événements
        foreach ($bookings as $booking) { // Parcours de tous les enregistrements
            $start = clone $booking->getBookAt(); // Clone la date de réservation
            $end = clone $start; // Clone la date de début
            // Fixe l'heure de début et de fin en fonction de la durée du produit
            $start->setTime(8, 0); // Début à 8h00
            if ($booking->getProduct()->getDuration() === Product::DURATION_HALF_DAY) { // Si la durée est une demi-journée
                if ($booking->getPeriod() === 'morning') { // Si la période est le matin
                    $end->setTime(12, 0); // Fin à 12h00
                } elseif ($booking->getPeriod() === 'afternoon') { // Si la période est l'après-midi
                    $start->setTime(13, 0);
                    $end->setTime(17, 0); // Fin à 17h00
                }
            } elseif ($booking->getProduct()->getDuration() === Product::DURATION_FULL_DAY) { // Si la durée est une journée complète
                $end->setTime(17, 0); // Fin à 17h00
            } elseif ($booking->getProduct()->getDuration() === Product::DURATION_TWO_DAYS) { // Si la durée est de deux jours
                $end->modify('+1 day')->setTime(17, 0); // Fin à 17h00 le lendemain
            }
            $events[] = [ // Ajout de l'événement
                'title' => $booking->getProduct()->getForfait(), // Ajout du forfait
                'start' => $start->format('Y-m-d\TH:i:s'), // Formatage de la date de début
                'end' => $end->format('Y-m-d\TH:i:s'), // Formatage de la date de fin
                'allDay' => false, // Désactive le mode journée entière
                'period' => $booking->getPeriod(),  // Ajout de la période
            ];
        }
        return new JsonResponse($events); // Retourne les événements au format JSON
    }

    #[Route('/booking/form', name: 'booking_form')]
    public function bookingForm(Request $request): Response
    {
        $date = $request->query->get('date'); // Récupère la date passée en paramètre
        $booking = new Booking(); // Crée une nouvelle réservation
        $dateTime = new \DateTime($date); // Convertit la date en objet DateTime
        $dateTime->setTime(8, 0);  // Fixe l'heure à 8h00
        $booking->setBookAt($dateTime); // Définit la date de réservation
        $form = $this->createForm(BookingType::class, $booking); // Crée le formulaire
        return $this->render('booking/form.html.twig', [
            'form' => $form->createView(), // Envoie le formulaire à la vue
        ]);
    }

    #[Route('/book', name: 'book', methods: ['GET', 'POST'])]
    public function book(Request $request, EntityManagerInterface $entityManager, BookingRepository $bookingRepository): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $booking->getProduct();
            // Vérifions la date reçue
            $date = $request->request->get('bookAt');
            if (!$date) {
                return new JsonResponse(['error' => 'La date est manquante.'], Response::HTTP_BAD_REQUEST);
            }
            $dateTime = new \DateTime($date);
            $booking->setBookAt($dateTime);
            // Fixe l'heure de début à 8h00 pour toutes les réservations
            $dateTime->setTime(8, 0);
            $booking->setBookAt($dateTime);
            // Choisissez la période si la durée est une demi-journée
            if ($product->getDuration() === Product::DURATION_HALF_DAY) {
                if ($booking->getPeriod() === null) {
                    $booking->setPeriod('morning'); // Default period
                }
                // Check if the selected period is already booked
                $existingBookings = $bookingRepository->findBy([
                    'bookAt' => $dateTime,
                    'period' => $booking->getPeriod(),
                ]);
                if (count($existingBookings) > 0) {
                    return new JsonResponse(['error' => 'La période sélectionnée est déjà réservée.'], Response::HTTP_CONFLICT);
                }
            } else {
                // Vérifiez si les dates sélectionnées chevauchent une réservation existante
            $endDateTime = clone $dateTime;
            $endDateTime->modify('+1 days');
            $existingBookings = $bookingRepository->findOverlappingBookings($dateTime, $endDateTime);
                if (count($existingBookings) > 0) {
                    return new JsonResponse(['error' => 'Les dates sélectionnées chevauchent une réservation existante'], Response::HTTP_CONFLICT);
                }
                $booking->setPeriod(null); // Ne pas définir de période pour les réservations d'une journée ou plus
            }
        
            // Set les informations du profil de l'utilisateur connecté
            $user = $this->getUser();
            $profile = $user->getProfile(); // Assurez-vous que la méthode getProfile existe dans votre entité User
            $booking->setProfile($profile);
            // Calculer le montant de la facture
            $price = $product->getTarifBase();
            if ($booking->getIsGroup()) {
                $participantCount = count($booking->getParticipants());
                $price *= $participantCount; // Multiplie le tarif de base par le nombre de participants
            }
            // Créer une facture
            $invoice = new Invoice();
            $invoice->setIssuedAt(new \DateTime());
            $invoice->setBooking($booking);
            $invoice->setAmount($price);
            $booking->setInvoice($invoice);
            $entityManager->persist($booking);
            $entityManager->persist($invoice);
            $entityManager->flush();
            return new JsonResponse(['success' => true,'invoiceId' => $invoice->getId()], Response::HTTP_OK);
        }

        return $this->render('booking/book.html.twig', [
            'form' => $form->createView(),
        ]);
    }
   
}
