<?php

namespace App\Controller;

use App\Repository\TicketRepository;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route; 
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
class TicketValidationController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('api/ticket/validation', name: 'app_ticket_validation')]
    public function validateTicket(Request $request, TicketRepository $ticketRepository): JsonResponse
    {
        $code = $request->request->get('code');



        if (!$code) {
            return new JsonResponse(['message' => 'Code is required'], 400);
        }

        // Rechercher le ticket par son code
        $ticket = $ticketRepository->findOneBy(['code' => $code]);

        if (!$ticket) {
            return new JsonResponse(['message' => 'Invalid ticket code'], 404);
        }

        // Vérifier si le ticket a déjà été utilisé
        if ($ticket->isClaimed()) {
            return new JsonResponse(['message' => 'Ticket has already been used'], 400);
        }

        // Marquer le ticket comme utilisé
        $ticket->setClaimed(true);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Ticket validated successfully',
            'prize' => $ticket->getLot()->getName(),
            'value' => $ticket->getLot()->getValue(),
        ]);
    }
}
