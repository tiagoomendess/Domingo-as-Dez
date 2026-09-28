# Dev image for Domingo às Dez (Laravel 5.8).
# PHP 7.4 is the last 7.x release that satisfies composer.lock
# (packages require ^7.2.5) without PHP 8 incompatibilities in Laravel 5.8.
#
# Build + run with docker compose (recommended):
#   docker compose up --build
# Then open http://localhost:8000
#
# The app container talks to MySQL on the host via host.docker.internal.
# Point DB_* in .env at your existing MySQL credentials.

FROM php:7.4-apache-bullseye

# PHP 7.4 images ship archived Debian. Use the main archive only;
# bullseye-security is no longer published on archive.debian.org.
RUN set -eux; \
    sed -i 's|https\?://deb.debian.org/debian|http://archive.debian.org/debian|g' /etc/apt/sources.list; \
    sed -i -E '/security/d; /-updates/d' /etc/apt/sources.list; \
    rm -f /etc/apt/sources.list.d/*.list; \
    printf 'Acquire::Check-Valid-Until "false";\n' > /etc/apt/apt.conf.d/99archive

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libicu-dev \
        libonig-dev \
        libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
        gd \
        bcmath \
        zip \
        exif \
        intl \
        pcntl \
        opcache \
    && a2enmod rewrite alias \
    && rm -rf /var/lib/apt/lists/*

# Composer 2.2 is the last line that supports PHP 7.4.
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

RUN sed -i 's/Listen 80/Listen 8000/' /etc/apache2/ports.conf \
    && echo 'ServerName localhost' >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY . /var/www/html

# /opt/vendor survives the bind mount of the project. The entrypoint copies
# it onto the named volume so PHP does not read vendor through Windows.
RUN composer install --no-interaction --prefer-dist --no-scripts \
    && md5sum composer.lock | awk '{print $$1}' > vendor/.lock-hash \
    && cp -a vendor /opt/vendor \
    && chown -R www-data:www-data /var/www/html \
    && find /var/www/html/storage /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \;

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
