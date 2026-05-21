FROM php:8.3-fpm-alpine

RUN apk add --no-cache bash curl git unzip icu-dev libzip-dev oniguruma-dev postgresql-dev nodejs npm \
    && docker-php-ext-install intl pdo pdo_pgsql zip opcache bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader || true \
    && npm install || true \
    && npm run build || true \
    && chmod -R ug+rw storage bootstrap/cache || true

EXPOSE 9000

CMD ["php-fpm"]
