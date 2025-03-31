<?php
namespace Deployer;

require 'recipe/symfony.php';

// Définir les hôtes par environnement
host('dev.example.com')
    ->set('deploy_path', '/var/www/dev')
    ->set('branch', 'develop');

host('prod.example.com')
    ->set('deploy_path', '/var/www/prod')
    ->set('branch', 'main');

// Configurations globales
set('repository', 'git@github.com:your-repo/your-project.git');
set('keep_releases', 5);

// Tâche principale
desc('Déployer le projet');
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

after('deploy:failed', 'deploy:unlock');
