.PHONY: up setup sh test phpstan phpstan-baseline cs-fix cs-check usage

usage:
	@echo ''
	@echo 'Facile.it MongoDB Bundle'
	@echo '========================'
	@echo ''
	@echo 'Docker targets'
	@echo ''
	@echo 'make setup: it installs dependencies, prepares docker-compose.override.yml, pulls images'
	@echo 'make sh: it creates containers and logs into the php one'
	@echo 'make stop: it stops the containers'
	@echo ''
	@echo 'Test targets'
	@echo ''
	@echo 'make test: run all tests with phpunit'
	@echo 'make phpstan: run phpstan analysis'
	@echo 'make cs-check: check code style'
	@echo 'make cs-fix: fix code style'
	@echo ''
	@echo 'Other targets'
	@echo ''
	@echo 'make phpstan-baseline: update the phpstan baseline'
	@echo ''

docker-compose.override.yml:
	cp docker-compose.override.yml.dist docker-compose.override.yml

docker-compose.yml: docker-compose.override.yml

setup: docker-compose.yml composer.json
	docker-compose run --rm php composer install

sh: docker-compose.yml
	docker-compose up -d --force-recreate
	docker exec -ti mb_php bash

stop: docker-compose.yml
	docker-compose stop

test:
	bin/phpunit tests

phpstan:
	bin/phpstan analyze --memory-limit=-1

phpstan-baseline:
	bin/phpstan analyze --memory-limit=-1 --generate-baseline

cs-fix:
	composer cs-fix

cs-check:
	composer cs-check
