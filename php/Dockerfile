# Usamos la imagen oficial de PHP 8.3-fpm
FROM php:8.3-fpm

# Instalar Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# --- PASO 1: INSTALAR EXTENSIONES DE PHP (AHORA VA PRIMERO) ---
# Instalar dependencias del sistema y extensiones de PHP necesarias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zlib1g-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install mysqli gd zip

# --- PASO 2: INSTALAR DEPENDENCIAS DE COMPOSER (AHORA VA DESPUÉS) ---
# Establecer el directorio de trabajo
WORKDIR /var/www/html/src

# Copiar los archivos de dependencias de Composer
COPY src/composer.json src/composer.lock ./

# Instalar las dependencias de Composer
RUN composer install --no-scripts --no-autoloader

# Generar el autoloader de Composer
RUN composer dump-autoload --optimize