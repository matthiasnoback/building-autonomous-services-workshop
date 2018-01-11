SHELL=/bin/bash

HOSTS_ENTRY:=127.0.0.1 dashboard.localhost sales.localhost purchase.localhost catalog.localhost

PLATFORM := $(shell uname -s)
ifeq ($(PLATFORM),Darwin)
export DOCKER_HOST_NAME_OR_IP := docker.for.mac.localhost
else ifeq ($(PLATFORM),Linux)
export DOCKER_HOST_NAME_OR_IP := $(shell ip -f inet addr show docker0 | grep -Po 'inet \K[\d.]+')
else
$(error Unable to automatically determine DOCKER_HOST_NAME_OR_IP: please provide it yourself by running: export DOCKER_HOST_NAME_OR_IP=...)
endif

ifeq ($(COMPOSER_HOME),)
export COMPOSER_HOME=~/.composer
endif

export HOST_UID := $(shell id -u)
export HOST_GID := $(shell id -g)

COMPOSER_RUN := docker run --rm --interactive --tty --volume ${PWD}:/app:cached --volume ${COMPOSER_HOME}:/tmp:cached --user ${HOST_UID}:${HOST_GID} composer:latest

## hosts-entry: Set up an entry for this project's host names in /etc/hosts
.PHONY: hosts-entry
hosts-entry:
	(grep "$(HOSTS_ENTRY)" /etc/hosts) || echo '$(HOSTS_ENTRY)' | sudo tee -a /etc/hosts

~/.composer:
	mkdir -p ~/.composer

vendor: ~/.composer composer.json composer.lock
	 ${COMPOSER_RUN} install

## composer: entrypoint for running Composer (use bin/composer.sh)
.PHONY: composer
composer:
	@${COMPOSER_RUN} $(ARGS) --ansi

## up: Start all services for this project
.PHONY: up
up: hosts-entry vendor
	docker-compose up -d --no-build --remove-orphans
	@echo "#########################################################"
	@echo ""
	@echo "Done, now open http://dashboard.localhost in your browser"
	@echo ""
	@echo "#########################################################"

## down: Stop and remove all containers and volumes for this project
.PHONY: down
down:
	docker-compose down --remove-orphans -v

docker/nginx/.built: docker/nginx/Dockerfile docker/nginx/template.conf
	docker build -t matthiasnoback/building_autonomous_services_nginx:latest -f docker/nginx/Dockerfile docker/nginx/
	touch $@

docker/php-fpm/.built: docker/php-fpm/Dockerfile docker/php-fpm/php.ini
	docker build -t matthiasnoback/building_autonomous_services_php_fpm:latest -f docker/php-fpm/Dockerfile docker/php-fpm/
	touch $@

docker/php-cli/.built: docker/php-cli/Dockerfile docker/php-cli/php.ini
	docker build -t matthiasnoback/building_autonomous_services_php_cli:latest -f docker/php-cli/Dockerfile docker/php-cli/
	touch $@

## build: Build the Docker images locally
.PHONY: build
build: docker/nginx/.built docker/php-fpm/.built docker/php-cli/.built

## push: Push the Docker images (administrator-only)
.PHONY: push
push: docker/nginx/.built docker/php-fpm/.built docker/php-cli/.built
	docker push matthiasnoback/building_autonomous_services_nginx:latest
	docker push matthiasnoback/building_autonomous_services_php_fpm:latest
	docker push matthiasnoback/building_autonomous_services_php_cli:latest

.PHONY: clean
clean:
	find . -name .built -type f | xargs rm -v

## destroy: remove everything to be able to start all over
.PHONY: destroy
destroy:
	rm -rv vendor
	rm -v var/*.*
	docker-compose down -v --remove-orphans --rmi all
