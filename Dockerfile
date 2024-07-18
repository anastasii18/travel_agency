FROM php:8.2-fpm-alpine

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

CMD bash -c "composer install"