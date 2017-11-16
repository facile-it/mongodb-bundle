FROM php:7.0.21-cli

MAINTAINER Alessandro Galli <a.galli85@gmail.com>

RUN \
  apt-get update && \
  apt-get install -y \
  wget

RUN echo "deb http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list.d/dotdeb.org.list && \
    echo "deb-src http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list.d/dotdeb.org.list && \
    wget -O- http://www.dotdeb.org/dotdeb.gpg | apt-key add -

RUN echo "deb http://repo.mongodb.org/apt/debian jessie/mongodb-org/3.4 main" >> /etc/apt/sources.list.d/mongodb-org-3.4.list && \
    apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 0C49F3730359A14518585931BC711F9BA15703C6

RUN export DEBIAN_FRONTEND=noninteractive

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
        vim \
        locales \
        openssl \
        mongodb-org-shell \
    && apt-get clean \
    && docker-php-ext-install -j5 \
        curl \
        opcache \
        intl \
        tidy \
        json \
        bz2 \
        mcrypt \
        xml \
        mbstring \
        zip

RUN pecl install mongodb-1.3.0 \
    && docker-php-ext-enable mongodb

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV TEST_ENV=docker

RUN useradd -m user-dev

USER user-dev

RUN mkdir -p /home/user-dev/project

WORKDIR /home/user-dev/project
