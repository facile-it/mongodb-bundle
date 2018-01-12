.PHONY: up setup start test

up: docker-compose.yml
	docker-compose up -d --force-recreate

setup: docker-compose.yml composer.json
	docker-compose run --rm php-cli composer install

start: up
	docker exec -ti mb_php bash

stop: docker-compose.yml
	docker-compose stop

test: docker-compose.yml phpunit.xml.dist
	docker-compose run --rm php-cli bash -c "sleep 3 && bin/phpunit -c phpunit.xml.dist"
