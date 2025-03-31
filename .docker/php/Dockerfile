FROM php:8.2-fpm-buster
ARG TIMEZONE

# Copier le fichier php.ini pour la configuration
COPY .docker/php/php.ini /usr/local/etc/php/conf.d/docker-php-config.ini

# Installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    gnupg \
    g++ \
    procps \
    openssl \
    git \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libfreetype6-dev \
    libpng-dev \
    libjpeg-dev \
    libicu-dev \
    libonig-dev \
    libxslt1-dev \
    acl \
    p7zip-full 




# Ceci mettra à jour Git à la version la plus récente disponible

# Configurer et installer les extensions PHP
RUN docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install \
    pdo pdo_mysql zip xsl gd intl opcache exif mbstring

# Configurer le fuseau horaire
RUN ln -snf  /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone \
    && printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini \
    && date

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer



# Définir le répertoire de travail
WORKDIR /var/www/symfony 
COPY . .
#COPY ../../.. . 
RUN composer install
