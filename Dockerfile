# === PHP 8.3 con Apache ===
FROM php:8.3-apache

ARG WWWUSER=1000
ARG WWWGROUP=1000

# Actualizar e instalar dependencias de sistema (Debian)
RUN apt-get update && apt-get install -y \
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
    nodejs \
    npm \
    ssl-cert \
    apache2-utils \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configurar e instalar extensiones de PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    zip

# Instalar y habilitar Redis
RUN pecl install redis && docker-php-ext-enable redis

# Habilitar módulos de Apache
RUN a2enmod rewrite ssl headers

# Generar certificado auto-firmado para pruebas
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/apache-selfsigned.key \
    -out /etc/ssl/certs/apache-selfsigned.crt \
    -subj "/C=ES/ST=Alicante/L=Alcoi/O=Batoi/OU=DAW/CN=projecteGrupX.es"

# Crear directorios solicitados y ajustar permisos
RUN mkdir -p /home/app/ftp/www /home/app/logs /home/backup/ftp/fitxers \
    && chown -R www-data:www-data /home/app /home/backup

# Crear un archivo de contraseñas para el backup (usuario: admin, pass: admin123)
RUN htpasswd -bc /etc/apache2/.htpasswd admin admin123

# Copiar configuración de Apache
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Enlazar la aplicación de Laravel al directorio solicitado (opcional, para cumplir con el path)
RUN ln -s /var/www/html/public /home/app/ftp/www/laravel

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo
WORKDIR /var/www/html

# Ajustar permisos para www-data
RUN usermod -u ${WWWUSER} www-data && groupmod -g ${WWWGROUP} www-data

# Asegurar que las carpetas de Laravel tengan los permisos correctos
RUN chown -R www-data:www-data /var/www/html

# Instalar dependencias si existe composer.json
RUN if [ -f composer.json ]; then \
    composer install --no-interaction --no-dev --optimize-autoloader; \
    fi

# Exponer puertos
EXPOSE 80 443
