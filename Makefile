.PHONY: usage
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

##################################################################
#
# RUN OUTISDE THE CONTAINER
#
##################################################################

docker-compose.override.yml:
	cp docker-compose.override.yml.dist docker-compose.override.yml

.PHONY: build-74 build-81 build-82
build-74:
	PHP_VERSION=7.4 MONGODB_EXTENSION_VERSION=1.6.0 docker-compose build
build-81:
	PHP_VERSION=8.1 MONGODB_EXTENSION_VERSION=1.12.0 docker-compose build
build-82:
	PHP_VERSION=8.1 MONGODB_EXTENSION_VERSION=1.15.0 docker-compose build

.PHONY: --setup-common setup setup-74 setup-81 setup-82
setup: setup-74
setup-74: | build-74 --setup-common
setup-81: | build-81 --setup-common
setup-82: | build-82 --setup-common
--setup-common: docker-compose.override.yml
	rm composer.lock || true
	docker-compose run --rm php composer install

.PHONY: sh stop
sh: docker-compose.yml
	docker-compose up -d --force-recreate
	docker exec -ti mb_php bash

stop: docker-compose.yml
	docker-compose stop


##################################################################
#
# RUN INSIDE THE CONTAINER
#
##################################################################

.PHONY: test coverage
test:
	bin/phpunit tests
coverage:
	bin/phpunit tests --coverage-clover=build/coverage-report.xml

.PHONY: phpstan phpstan-baseline
phpstan:
	bin/phpstan analyze --memory-limit=-1

phpstan-baseline:
	bin/phpstan analyze --memory-limit=-1 --generate-baseline

.PHONY: cs-fix cs-check
cs-fix:
	composer cs-fix
cs-check:
	composer cs-check
