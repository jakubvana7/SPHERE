FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip curl git \
    && docker-php-ext-install pdo pdo_mysql mysqli zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN git config --global --add safe.directory /var/www/html \
    && composer install --no-dev --optimize-autoloader --no-interaction

# Symlink images into www so they're web-accessible
RUN ln -sfn /var/www/html/images /var/www/html/www/images

# Writable directories for Nette
RUN mkdir -p log temp \
    && chmod -R 777 log temp

COPY .docker/apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
