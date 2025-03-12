# Usa la imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instala dependencias y herramientas necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    nano \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Habilita mod_rewrite para Apache
RUN a2enmod rewrite

# Copia el contenido del proyecto al contenedor
COPY . /var/www/html

# Establece los permisos para el directorio de Laravel
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/storage
RUN chmod -R 775 /var/www/html/bootstrap/cache

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Instala Composer (si aún no lo has hecho)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala las dependencias de PHP (Composer)
RUN composer install --no-dev --optimize-autoloader

# Expone el puerto 80 para la aplicación web
EXPOSE 80
