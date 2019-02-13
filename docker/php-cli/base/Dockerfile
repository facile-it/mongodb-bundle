ARG PHP_VERSION
FROM php:${PHP_VERSION}-cli

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update \
    && apt-get install -y \
        libbz2-dev \
        libcurl4-openssl-dev \
        libedit-dev \
        libfontconfig \
        libgmp-dev \
        libicu-dev \
        libmcrypt-dev \
        libssl-dev \
        libtidy-dev \
        libxml2-dev \
        libxslt-dev \
        libzip-dev \
        locales \
        openssl \
    && apt-get clean

RUN docker-php-ext-install -j5 \
        curl \
        opcache \
        intl \
        tidy \
        json \
        bz2 \
        xml \
        mbstring \
        zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer global require hirak/prestissimo

ARG MONGODB_EXTENSION_VERSION
RUN pecl install "mongodb-${MONGODB_EXTENSION_VERSION}" \
    && docker-php-ext-enable mongodb

ENV TEST_ENV=docker

RUN useradd -m user-dev

USER user-dev

RUN mkdir -p /home/user-dev/project

WORKDIR /home/user-dev/project
