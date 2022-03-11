FROM php:7.3

COPY . /var/www/html
WORKDIR /var/www/html

RUN chmod -R 777 .

RUN apt-get update && apt-get install -y libpq-dev git libzip-dev

RUN docker-php-ext-install pdo pdo_pgsql zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=2.2.7

RUN composer install

CMD php artisan serve --host=0.0.0.0 --port=$PORT