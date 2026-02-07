FROM php:8.4-apache

# 1. Install dependensi sistem jika diperlukan dan ekstensi PHP
# Kita gabungkan RUN untuk mengurangi jumlah layer image
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite

# 2. Set working directory
WORKDIR /var/www/html

# 3. Salin file aplikasi
# Pastikan folder 'src' ada di direktori yang sama dengan Dockerfile ini
COPY src/ .

# 4. Atur permission
RUN chown -R www-data:www-data /var/www/html
