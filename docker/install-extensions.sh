#!/bin/bash

set -euo pipefail
IFS=$'\n\t'

# Is everything necessary?
docker-php-ext-install curl
docker-php-ext-install opcache
docker-php-ext-install intl
docker-php-ext-install tidy
docker-php-ext-install bz2
docker-php-ext-install xml
docker-php-ext-install mbstring
docker-php-ext-install zip

if echo "$PHP_VERSION" | grep -Eq '7\.\d*'
then
  docker-php-ext-install json
fi

pecl install pcov && docker-php-ext-enable pcov
