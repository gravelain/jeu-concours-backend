<?php // src/Command/GenerateTicketsCommand.php
namespace App\Command;

use App\Entity\Ticket;
use App\Entity\Lot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-tickets',
    description: 'Génère et stocke des codes de tickets dans la base de données'
)]
class GenerateTicketsCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $numberOfTickets = 500000; // Nombre total de tickets à générer

        // Récupération des lots depuis la base de données
        $lots = $this->entityManager->getRepository(Lot::class)->findAll();
        $lotDistribution = $this->getLotDistribution($lots, $numberOfTickets);

        for ($i = 0; $i < $numberOfTickets; $i++) {
            $ticket = new Ticket();
            $ticket->setCode($this->generateTicketCode());
            $ticket->setClaimed(false);
            
            $lot = $this->getLotForTicket($lotDistribution);
            // Assurez-vous que ce lot est bien une entité existante de la base de données
            $ticket->setLot($lot);

            $this->entityManager->persist($ticket);

            if ($i % 1000 === 0) {
                // Flush every 1000 records to avoid memory issues
                $this->entityManager->flush();
                $this->entityManager->clear();

                // Recharger les lots après un clear
                $lots = $this->entityManager->getRepository(Lot::class)->findAll();
                $lotDistribution = $this->getLotDistribution($lots, $numberOfTickets - $i - 1);
            }
        }

        $this->entityManager->flush(); // Flush remaining records

        $io->success('Les tickets ont été générés et stockés avec succès !');

        return Command::SUCCESS;
    }

    private function generateTicketCode($length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function getLotDistribution(array $lots, int $numberOfTickets): array
    {
        $distribution = [];
        foreach ($lots as $lot) {
            $count = ($lot->getPourcentage() / 100) * $numberOfTickets;
            for ($i = 0; $i < $count; $i++) {
                $distribution[] = $lot;
            }
        }

        shuffle($distribution); // Mélanger les lots pour une distribution aléatoire

        return $distribution;
    }

    private function getLotForTicket(array &$lotDistribution): Lot
    {
        return array_pop($lotDistribution);
    }
}
