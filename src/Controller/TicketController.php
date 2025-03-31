<?php

namespace App\Controller;

use App\Entity\Ticket;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TicketRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class TicketController extends AbstractController
{
    private $doctrine;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ManagerRegistry $doctrine, private TicketRepository $ticketRepository, private Security $security)
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $entityManager;
    }
    
    #[Route('/tikets', name: 'tikets')]
    public function index(): Response
    {
        // Récupérer tous les tickets
        $tickets = $this->doctrine->getRepository(Ticket::class)->findAll();

        // Créer un tableau pour compter les lots
        $lotCounts = [];
        $lotTickets = [];

        // Parcourir les tickets et compter les lots
        foreach ($tickets as $ticket) {
            $lotName = $ticket->getLot()->getName();
            if (!isset($lotCounts[$lotName])) {
                $lotCounts[$lotName] = 0;
                $lotTickets[$lotName] = [];
            }
            $lotCounts[$lotName]++;
            $lotTickets[$lotName][] = $ticket;
        }

        return $this->render('ticket/index.html.twig', [
            'lotCounts' => $lotCounts,
            'lotTickets' => $lotTickets,
        ]);
    }

    /* Route pour récupérer les lots d'un utilisateur */
    #[Route('api/user/lot', name: 'user_prizes', methods:['get'])]
    public function getUserPrizes(): JsonResponse
    {
        $user = $this->security->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'User not authenticated'], 401);
        }

        $tickets = $this->ticketRepository->findBy(['user' => $user]);

        $prizes = [];
        foreach ($tickets as $ticket) {
            $lot = $ticket->getLot();
            if ($lot) {
                $prizes[] = [
                    'code' => $ticket->getCode(),
                    'prize_name' => $lot->getName(),
                    'prize_value' => $lot->getValue(),
                ];
            }
        }

        return new JsonResponse($prizes);
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

    #[Route('/api/ticket/search', name: 'search_ticket', methods: ['GET'])]
    public function searchTicket(Request $request): JsonResponse
    {
        // Récupérer le code du ticket depuis la requête (par exemple dans l'URL ?code=XXX)
        $code = $request->query->get('code');

        if (!$code) {
            return new JsonResponse(['error' => 'Ticket code is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Chercher le ticket par son code
        $ticket = $this->ticketRepository->findTicketByCode($code);

        if (!$ticket) {
            return new JsonResponse(['error' => 'Ticket not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Si le ticket est trouvé, retourner ses informations
        return new JsonResponse([
            'id' => $ticket->getId(),
            'code' => $ticket->getCode(),
            'is_claimed' => $ticket->isClaimed(),
            'lot' => $ticket->getLot(),
            'user' => $ticket->getUser(),
        ]);
    }
}
