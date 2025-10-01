FROM php:8.4-fpm

WORKDIR /app

# Install dependencies and extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    $PHPIZE_DEPS \
    git cmake libxml2-dev libpq-dev libzip-dev zlib1g-dev libicu-dev unzip \
 && rm -rf /var/lib/apt/lists/*

# Build bundled extensions
RUN docker-php-ext-install -j"$(nproc)" dom intl opcache zip pdo pdo_pgsql

# Install Xdebug via PECL
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
