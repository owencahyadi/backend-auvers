# Stage 1: Build dependencies using Composer
FROM composer:latest AS composer-stage
WORKDIR /app
COPY . /app
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Stage 2: Production Apache/PHP image
FROM php:8.2.19-apache

# Install system dependencies & PostgreSQL PHP extension
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql

# Set working directory
WORKDIR /var/www/html

# Copy project files from composer stage
COPY --from=composer-stage /app /var/www/html

# Set permissions for Laravel storage & cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Change Apache document root to public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -s 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

EXPOSE 80
CMD ["apache2-foreground"]