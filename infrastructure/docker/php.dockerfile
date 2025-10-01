FROM php:8.4-fpm
WORKDIR /app

# Build deps for DOM/intl/zip on PHP 8.4 (Lexbor is vendored; do NOT build it yourself)
RUN apt-get update && apt-get install -y --no-install-recommends \
    $PHPIZE_DEPS git cmake libxml2-dev libpq-dev libzip-dev zlib1g-dev libicu-dev unzip \
 && rm -rf /var/lib/apt/lists/*

# Ensure headers from the PHP source root are visible during the DOM/Lexbor build
ENV CPPFLAGS="-I/usr/src/php"

# Build the bundled extensions
RUN docker-php-ext-install -j"$(nproc)" dom intl opcache zip pdo pdo_pgsql

# Xdebug + Composer
RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
