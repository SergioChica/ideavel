# Usa una imagen base de PHP con FPM y las extensiones necesarias
FROM php:8.1-fpm

# Instala las dependencias del sistema (como Composer, extensiones PHP, Node.js y otros)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev \
    # Instala Node.js (requiere un PPA para la versión más reciente)
    && curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    # Instala Composer
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configura el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copia todos los archivos del proyecto Laravel al contenedor
COPY . .

# Instala las dependencias de PHP de Composer
RUN composer install --no-dev --optimize-autoloader

# Instala las dependencias de frontend con Node.js (npm o yarn)
RUN npm install

# Realiza una compilación de los activos frontales (Vite)
RUN npm run build

# Expone el puerto para PHP-FPM
EXPOSE 9000

# Comando por defecto para ejecutar PHP-FPM
CMD ["php-fpm"]
