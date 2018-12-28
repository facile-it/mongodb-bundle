.PHONY: up setup start test

docker-compose.override.yml:
	cp docker-compose.override.yml.dist docker-compose.override.yml

docker-compose.yml: docker-compose.override.yml

up: docker-compose.yml
	docker-compose up -d --force-recreate

setup: docker-compose.yml composer.json
	docker-compose run --rm php-cli composer install
	docker-compose run --rm webapp composer install

login:
	docker exec -ti mb_php bash

start: up
	docker-compose port webapp 8000

stop: docker-compose.yml
	docker-compose stop

test: docker-compose.yml phpunit.xml.dist
	docker-compose run --rm php-cli bash -c "sleep 3 && bin/phpunit -c phpunit.xml.dist"
