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

# No version of xdebug for php-7.3
RUN if echo "$PHP_VERSION" | grep -Eq '7\.3\.\d*'; \
    then echo "no-xdebug"; \
    else \
        yes | pecl install xdebug \
        && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
        && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
        && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini; \
    fi

ARG MONGODB_EXTENSION_VERSION

# Openssl1.1 is not compatible with ext 1.1.5
RUN if [ "${MONGODB_EXTENSION_VERSION}" = "1.1.5" ]; \
    then apt-get install -y libssl1.0; \
    fi

RUN pecl install mongodb-${MONGODB_EXTENSION_VERSION} \
    && docker-php-ext-enable mongodb

ENV TEST_ENV=docker

RUN useradd -m user-dev

USER user-dev

RUN mkdir -p /home/user-dev/project

WORKDIR /home/user-dev/project
