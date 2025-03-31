<?php

namespace App\Controller;
use App\Service\LotteryService;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository , private LotteryService $lotteryService)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/api/contest/participants', name: 'contest_participants', methods: ['GET'])]
    public function getContestParticipants(): JsonResponse
    {
        // Récupérer les utilisateurs ayant au moins un ticket (participants)
        $participants = $this->userRepository->findUsersWithTickets();

        $data = [];
        foreach ($participants as $user) {
            $data[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'tickets' => count($user->getTikets()),
            ];
        }

        return new JsonResponse($data, 200);
    }
    
      #[Route('/api/lottery/pick-winner', name: 'api_lottery_pick_winner', methods: ['GET'])]
    public function pickWinner(): JsonResponse
    {
        $winner = $this->lotteryService->pickRandomWinner();

        if (!$winner) {
            return new JsonResponse(['error' => 'No participants found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $winner->getId(),
            'firstName' => $winner->getFirstName(),
            'email' => $winner->getEmail(),
            'message' => 'Congratulations to the winner!'
        ]);
    }
}
