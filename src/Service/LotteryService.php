<?php 
// src/Service/LotteryService.php
namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class LotteryService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function pickRandomWinner(): ?User
    {
        $repository = $this->entityManager->getRepository(User::class);

        // Get all participants
        $participants = $repository->findUsersWithTickets();

        if (empty($participants)) {
            return null;
        }

        // Pick a random participant
        $randomKey = array_rand($participants);
        return $participants[$randomKey];
    }
}
