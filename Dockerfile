FROM php:8.4-apache

# Install dependensi sistem
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite \
    && a2enmod headers

# Set working directory
WORKDIR /var/www/html

# PHP optimization for high concurrency
RUN echo "pm = dynamic" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "pm.max_children = 50" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "pm.start_servers = 10" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "pm.min_spare_servers = 5" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "pm.max_spare_servers = 20" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "pm.max_requests = 500" >> /usr/local/etc/php-fpm.d/www.conf

# Apache optimization
RUN echo "Timeout 60" >> /etc/apache2/apache2.conf && \
    echo "KeepAlive On" >> /etc/apache2/apache2.conf && \
    echo "MaxKeepAliveRequests 100" >> /etc/apache2/apache2.conf && \
    echo "KeepAliveTimeout 5" >> /etc/apache2/apache2.conf

# Salin file aplikasi
COPY src/ .

# Atur permission - JANGAN overwrite ownership, cukup chmod
RUN chmod -R 777 /var/www/html/uploads /var/www/html/ajax /var/www/html/temp 2>/dev/null || true
RUN chown -R www-data:www-data /var/www/html

# PENTING: Buat folder uploads dengan permission 777
RUN mkdir -p /var/www/html/uploads && chmod 777 /var/www/html/uploads
