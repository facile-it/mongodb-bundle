#!/usr/bin/env bash

docker-compose start
docker exec -ti mb_php bin/phpunit -c phpunit.xml.dist
