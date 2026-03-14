FROM php:8.4-apache

# Install dependensi sistem
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Salin file aplikasi
COPY src/ .

# Atur permission - JANGAN overwrite ownership, cukup chmod
RUN chmod -R 777 /var/www/html/uploads /var/www/html/ajax /var/www/html/temp 2>/dev/null || true
RUN chown -R www-data:www-data /var/www/html

# PENTING: Buat folder uploads dengan permission 777
RUN mkdir -p /var/www/html/uploads && chmod 777 /var/www/html/uploads
