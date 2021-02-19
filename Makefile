.PHONY: up setup start test

docker-compose.override.yml:
	cp docker-compose.override.yml.dist docker-compose.override.yml

docker-compose.yml: docker-compose.override.yml

up: docker-compose.yml
	docker-compose up -d --force-recreate

setup: docker-compose.yml composer.json
	docker-compose run --rm php composer install

start: up
	docker exec -ti mb_php bash

stop: docker-compose.yml
	docker-compose stop

test: docker-compose.yml phpunit.xml.dist
	docker-compose run --rm php bash -c "bin/phpunit -c phpunit.xml.dist"

phpstan: docker-compose.yml
	docker-compose run --rm php bash -c "bin/phpstan analyze --memory-limit=-1 -l7 src/ tests/"

setup-symfony-%: SYMFONY_VERSION = $*
setup-symfony-%:
	rm composer.lock || true
	docker-compose run --no-deps --rm php composer require-dev "symfony/symfony:${SYMFONY_VERSION}" --no-update;
	docker-compose run --no-deps --rm php composer install --prefer-dist --no-interaction ${COMPOSER_FLAGS}

test-composer-install: setup-symfony-3.4 setup-symfony-4.3 setup-symfony-4.4
