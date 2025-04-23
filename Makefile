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
DOCKER_COMPOSE ?= $(shell (command -v docker-compose > /dev/null && echo "docker-compose") || (command -v docker > /dev/null && echo "docker compose"))

docker-compose.override.yml:
	cp docker-compose.override.yml.dist docker-compose.override.yml

.PHONY: --build --setup-common
--build:
	PHP_VERSION=$(PHP_VERSION) MONGODB_EXTENSION_VERSION=$(MONGODB_EXTENSION_VERSION) MONGODB_VERSION=$(MONGODB_VERSION) $(DOCKER_COMPOSE) config
	PHP_VERSION=$(PHP_VERSION) MONGODB_EXTENSION_VERSION=$(MONGODB_EXTENSION_VERSION) MONGODB_VERSION=$(MONGODB_VERSION) $(DOCKER_COMPOSE) build

--setup-common: docker-compose.override.yml
	rm composer.lock || true
	PHP_VERSION=$(PHP_VERSION) MONGODB_EXTENSION_VERSION=$(MONGODB_EXTENSION_VERSION) MONGODB_VERSION=$(MONGODB_VERSION) $(DOCKER_COMPOSE) up -d --force-recreate
	PHP_VERSION=$(PHP_VERSION) MONGODB_EXTENSION_VERSION=$(MONGODB_EXTENSION_VERSION) MONGODB_VERSION=$(MONGODB_VERSION) $(DOCKER_COMPOSE) exec php composer install

.PHONY: setup setup-81
setup: setup-81

.PHONY: setup-81
setup-81: PHP_VERSION=8.1
setup-81: MONGODB_EXTENSION_VERSION=1.12.0
setup-81: MONGODB_VERSION=5.0
setup-81: | --build --setup-common

.PHONY: setup-82
setup-82: PHP_VERSION=8.2
setup-82: MONGODB_EXTENSION_VERSION=1.15.0
setup-82: MONGODB_VERSION=6.0
setup-82: | --build --setup-common

.PHONY: setup-84
setup-84: PHP_VERSION=8.4
setup-84: MONGODB_EXTENSION_VERSION=2.0.0
setup-84: MONGODB_VERSION=6.0
setup-84: | --build --setup-common

.PHONY: sh stop
sh: docker-compose.yml
	$(DOCKER_COMPOSE) exec php bash

stop: docker-compose.yml
	$(DOCKER_COMPOSE) down --volumes

.PHONY: test-docker-targets
test-docker-targets:
	$(MAKE) stop
	$(MAKE) setup-81
	$(DOCKER_COMPOSE) exec php bash -c "php -v | head -1 | cut -d ' ' -f 2 | grep -Eq '8\.1\.\d*'"
	$(DOCKER_COMPOSE) exec php make test
	$(MAKE) stop
	$(MAKE) setup-82
	$(DOCKER_COMPOSE) exec php bash -c "php -v | head -1 | cut -d ' ' -f 2 | grep -Eq '8\.2\.\d*'"
	$(DOCKER_COMPOSE) exec php make test
	$(MAKE) stop
	$(MAKE) setup-84
	$(DOCKER_COMPOSE) exec php bash -c "php -v | head -1 | cut -d ' ' -f 2 | grep -Eq '8\.4\.\d*'"
	$(DOCKER_COMPOSE) exec php make test
	$(MAKE) stop
	@echo
	@echo "Test passed"


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

.PHONY: rector rector-apply
rector:
	bin/rector process --dry-run
rector-apply:
	bin/rector process
