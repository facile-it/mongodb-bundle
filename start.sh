#!/usr/bin/env bash

docker-compose up -d --force-recreate
docker exec -ti mb_php bash
