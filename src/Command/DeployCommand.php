<?php 
// src/Command/DeployCommand.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:deploy:dev',
    description: 'Crée les lots dans la base de données'
)]
class DeployCommand extends Command
{
  
    protected function configure(): void
    {
        $this
            ->setDescription('Deploys the application to the development environment')
            ->addOption('env', null, InputOption::VALUE_REQUIRED, 'Environment to deploy to', 'dev');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $env = $input->getOption('env');
        $output->writeln('Deploying to environment: ' . $env);

        // Place your deployment logic here

        return Command::SUCCESS;
    }
}
