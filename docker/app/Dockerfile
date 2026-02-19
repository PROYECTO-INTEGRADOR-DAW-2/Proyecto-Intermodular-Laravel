FROM php:8.3-apache

ARG WWWUSER=1000
ARG WWWGROUP=1000

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    ssl-cert \
    apache2-utils \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd intl zip \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite ssl headers vhost_alias

RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/apache-selfsigned.key \
    -out /etc/ssl/certs/apache-selfsigned.crt \
    -subj "/C=ES/ST=Alicante/L=Alcoi/O=Batoi/OU=DAW/CN=projectegrupb.es" \
    -addext "subjectAltName = DNS:projectegrupb.es, DNS:app.projectegrupb.es, DNS:backup.projectegrupb.es"

RUN mkdir -p /home/app/logs /home/backupuser/ftp/fitxers && \
    chown -R www-data:www-data /home/app/logs /home/backupuser/ftp/fitxers && \
    htpasswd -bc /etc/apache2/.htpasswd admin admin123

COPY docker/app/apache.conf /etc/apache2/sites-available/000-default.conf
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /home/app/ftp

RUN usermod -u ${WWWUSER} www-data && groupmod -g ${WWWGROUP} www-data

EXPOSE 80 443
