FROM php:7.2-cli

RUN apt update \
    && apt install -y zip unzip openssl libssl-dev libcurl4-openssl-dev \
    && pecl install mongodb-1.3.0 \
    && docker-php-ext-enable mongodb \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer global require hirak/prestissimo

RUN useradd -m user-dev

USER user-dev

RUN mkdir -p /home/user-dev/webapp

WORKDIR /home/user-dev/webapp
