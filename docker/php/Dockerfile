FROM php:8.2-fpm

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --optimize-autoloader \
    && mkdir -p templates_c cache \
    && chown -R www-data:www-data templates_c cache
