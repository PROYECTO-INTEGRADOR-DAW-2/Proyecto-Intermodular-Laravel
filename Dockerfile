# Usamos la versión Alpine si fuera posible, pero para no romperte Apache,
# seguimos con la tuya pero limpiando a muerte.
FROM php:8.3-apache

ARG WWWUSER=1000
ARG WWWGROUP=1000

# 1. Juntamos TODO lo que sea instalación en un solo paso para no crear capas extra
# 2. QUITAMOS node y npm (ocupa ~200MB-400MB). Haz el build de JS en tu PC y sube el resultado.
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

# Habilitar módulos de Apache
RUN a2enmod rewrite ssl headers vhost_alias

# Generar certificado (Esto no pesa mucho, lo dejamos)
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/apache-selfsigned.key \
    -out /etc/ssl/certs/apache-selfsigned.crt \
    -subj "/C=ES/ST=Alicante/L=Alcoi/O=Batoi/OU=DAW/CN=projecteGrupX.es" \
    -addext "subjectAltName = DNS:projecteGrupX.es, DNS:*.projecteGrupX.es, DNS:*.test.projecteGrupX.es"

# Combinamos la creación de directorios y permisos para ahorrar capas
RUN mkdir -p /home/app/logs /home/tests/logs /home/tests/ftp && \
    chown -R www-data:www-data /home/app/logs /home/tests/logs /home/tests/ftp && \
    htpasswd -bc /etc/apache2/.htpasswd admin admin123

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /home/app/ftp

# Ajustar usuarios
RUN usermod -u ${WWWUSER} www-data && groupmod -g ${WWWGROUP} www-data

# OJO: No hagas el composer install DENTRO del build si vas justo de disco.
# Es mejor hacerlo después con el contenedor arrancado o usar el truco del ignore.
# Pero si lo dejas, asegúrate de que el .dockerignore funcione.

COPY scripts/backup.sh /usr/local/bin/backup
RUN chmod +x /usr/local/bin/backup

EXPOSE 80 443
