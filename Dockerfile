# Utiliser une image PHP avec FPM et les extensions nécessaires
FROM php:8.2-fpm

# Définir le répertoire de travail
WORKDIR /var/www/html

# Installer les dépendances système et extensions PHP requises par Symfony
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    && docker-php-ext-install pdo_mysql zip intl opcache 

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Ajouter un utilisateur non-root
RUN useradd -m -d /home/symfony -s /bin/bash symfony && \
    chown -R symfony:symfony /var/www/html

# Copier le code source dans le conteneur
COPY --chown=symfony:symfony . .

# Passer en mode utilisateur symfony
USER symfony

# Installer les dépendances PHP selon l'environnement
ARG APP_ENV=prod
RUN if [ "$APP_ENV" != "prod" ]; then composer install --prefer-dist --no-progress --no-interaction --no-scripts; else composer install --no-dev --optimize-autoloader --no-progress --no-interaction --no-scripts; fi

# Exposer le port 9000 pour PHP-FPM
EXPOSE 9000

# Commande pour démarrer PHP-FPM
CMD ["php-fpm"]
