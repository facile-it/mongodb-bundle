.PHONY: up setup start test

up:
	docker-compose up -d --force-recreate

setup:
	docker-compose run --rm php-cli composer install

start: up
	docker exec -ti mb_php bash

test:
	docker-compose run --rm php-cli bash -c "sleep 3 && bin/phpunit -c phpunit.xml.dist"
