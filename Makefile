.PHONY: up setup start test

docker-compose.override.yml:
	cp docker-compose.override.yml.dist docker-compose.override.yml

docker-compose.yml: docker-compose.override.yml

up: docker-compose.yml
	docker-compose up -d --force-recreate

setup: docker-compose.yml composer.json
	docker-compose run --rm php-cli composer install

start: up
	docker exec -ti mb_php bash

stop: docker-compose.yml
	docker-compose stop

cs-check: .php_cs.dist
	docker-compose run --rm --no-deps php-cli bash -c "bin/php-cs-fixer fix --ansi --verbose --diff --dry-run"
	
cs-fix: .php_cs.dist
	docker-compose run --rm --no-deps php-cli bash -c "bin/php-cs-fixer fix --ansi --verbose"

phpstan: docker-compose.yml phpstan.neon
	docker-compose run --rm --no-deps php-cli bash -c "bin/phpstan analyze"

test: docker-compose.yml phpunit.xml.dist
	docker-compose run --rm php-cli bash -c "bin/phpunit -c phpunit.xml.dist"
