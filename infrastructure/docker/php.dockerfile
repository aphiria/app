FROM php:8.4-fpm

WORKDIR /app

# Install dependencies and extensions
RUN apt-get update && apt-get install -y git libxml2-dev libpq-dev libzip-dev liblexbor-dev unzip
RUN docker-php-ext-install dom intl opcache zip

# Install Xdebug via PECL
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
