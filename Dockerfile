# Usa una imagen base con PHP y Nginx
FROM php:8.1-fpm

# Instala las dependencias necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    curl \
    nginx \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    # Instala Node.js y Composer
    && curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configura el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copia todos los archivos del proyecto Laravel al contenedor
COPY . .

# Instala las dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# Instala las dependencias de Node.js y compila los activos
RUN npm install && npm run build

# Configura Nginx
COPY ./nginx/default.conf /etc/nginx/sites-available/default

# Exponer los puertos para Nginx (80) y PHP-FPM (9000)
EXPOSE 80
EXPOSE 9000

# Inicia Nginx y PHP-FPM
CMD service nginx start && php-fpm
