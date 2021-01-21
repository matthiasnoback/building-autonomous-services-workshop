SHELL=/bin/bash

export HOST_UID := $(shell id -u)
export HOST_GID := $(shell id -g)

COMPOSER_RUN := docker run --rm --interactive --tty --volume `pwd`:/app:cached --user ${HOST_UID}:${HOST_GID} composer:latest

DOCKER_COMPOSE_ALL := docker-compose -f docker-compose.web.yml -f docker-compose.consumers.yml
DOCKER_COMPOSE_CONSUMERS := docker-compose -f docker-compose.consumers.yml
DOCKER_COMPOSE_WEB := docker-compose -f docker-compose.web.yml
DOCKER_COMPOSE_TEST := docker-compose -f docker-compose.test.yml

.PHONY: vendor
vendor:
	 ${COMPOSER_RUN} install

## up: Start all services for this project
.PHONY: up
up: vendor
	${DOCKER_COMPOSE_ALL} up -d --no-build --remove-orphans
	@echo "#############################################################"
	@echo ""
	@echo "Done, now open http://dashboard.localtest.me in your browser"
	@echo ""
	@echo "#############################################################"

## restart: Restart the consumers
.PHONY: restart
restart:
	${DOCKER_COMPOSE_CONSUMERS} stop
	${DOCKER_COMPOSE_ALL} up -d

## down: Stop and remove all containers and volumes for this project
.PHONY: down
down:
	${DOCKER_COMPOSE_ALL} down --remove-orphans -v

## test: Start all services and run the tests
.PHONY: test
test: cleanup restart
	${DOCKER_COMPOSE_TEST} run --rm test sh ./run_tests.sh

## ps: Show the status of the containers
.PHONY: ps
ps:
	${DOCKER_COMPOSE_ALL} ps

## logs: Show and follow the container logs
.PHONY: logs
logs:
	${DOCKER_COMPOSE_ALL} logs -f

## build: Build the Docker images locally
.PHONY: build
build:
	docker build -t matthiasnoback/building_autonomous_services_nginx:latest -f docker/nginx/Dockerfile docker/nginx/
	docker build -t matthiasnoback/building_autonomous_services_php_fpm:latest -f docker/php-fpm/Dockerfile docker/php-fpm/
	docker build -t matthiasnoback/building_autonomous_services_php_cli:latest -f docker/php-cli/Dockerfile docker/php-cli/

## push: Push the Docker images (administrator-only)
.PHONY: push
push:
	docker push matthiasnoback/building_autonomous_services_nginx:latest
	docker push matthiasnoback/building_autonomous_services_php_fpm:latest
	docker push matthiasnoback/building_autonomous_services_php_cli:latest

## destroy: remove everything to be able to start all over
.PHONY: destroy
destroy: cleanup
	rm -rvf vendor/*
	${DOCKER_COMPOSE_ALL} down -v --remove-orphans --rmi all

## cleanup: remove all database files in var/
.PHONY: cleanup
cleanup:
	rm -v var/*.json var/stream.txt || true
