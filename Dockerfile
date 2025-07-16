FROM php:8.2-cli

RUN apt-get update && \
    apt-get install -y  git unzip curl libcurl4-openssl-dev && \
    docker-php-ext-install curl
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN echo "memory_limit = -1" > /usr/local/etc/php/conf.d/memory-limit.ini

WORKDIR /app
COPY . .

RUN composer install --no-interaction --prefer-dist
CMD ["php", "daemon.php"]
