#!/bin/sh
set -e

# Créer le lien symbolique pour la configuration de Nginx, si il n'existe pas
if [ ! -e /etc/nginx/sites-enabled/default ]; then
    ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/
fi

# Vérifier la configuration Nginx
nginx -t

# Démarrer Nginx en arrière-plan
service nginx start

# Démarrer PHP-FPM
php-fpm
