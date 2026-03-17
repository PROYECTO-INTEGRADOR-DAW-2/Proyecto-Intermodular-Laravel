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
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd intl zip \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers vhost_alias

RUN mkdir -p /home/app/logs /home/backupuser/ftp/fitxers && \
    chown -R www-data:www-data /home/app/logs /home/backupuser/ftp/fitxers

COPY docker/app/apache.conf /etc/apache2/sites-available/000-default.conf
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /home/app/ftp

RUN usermod -u ${WWWUSER} www-data && groupmod -g ${WWWGROUP} www-data

# Configurar git para evitar errores de "dubious ownership" a nivel de sistema
RUN git config --system --add safe.directory /home/app/ftp

EXPOSE 80 443
