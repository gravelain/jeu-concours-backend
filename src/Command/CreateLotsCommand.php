<?php 
// src/Command/CreateLotsCommand.php

namespace App\Command;

use App\Entity\Lot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-lots',
    description: 'Crée les lots dans la base de données'
)]
class CreateLotsCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        // Vous pouvez laisser vide ou ajouter des options/arguments ici.
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $lots = [
            ['name' => 'Infuseur à thé', 'value' => 10, 'percentage' => 60],
            ['name' => 'Boite de 100g de thé détox ou infusion', 'value' => 20, 'percentage' => 20],
            ['name' => 'Boite de 100g de thé signature', 'value' => 30, 'percentage' => 10],
            ['name' => 'Coffret découverte (39€)', 'value' => 39, 'percentage' => 6],
            ['name' => 'Coffret découverte (69€)', 'value' => 69, 'percentage' => 4],
        ];

        foreach ($lots as $lotData) {
            $lot = new Lot();
            $lot->setName($lotData['name']);
            $lot->setValue($lotData['value']);
            $lot->setPourcentage($lotData['percentage']);
            $this->entityManager->persist($lot);
        }

        $this->entityManager->flush();

        $io->success('Les lots ont été créés avec succès !');

        return Command::SUCCESS;
    }
}

