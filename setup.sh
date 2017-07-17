#!/usr/bin/env bash

docker-compose up -d --build --force-recreate
docker exec -ti mb_php composer install
docker-compose stop
