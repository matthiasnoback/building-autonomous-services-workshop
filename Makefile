# This line allows for using spaces instead of tabs for indentation
SHELL=/bin/bash

HOSTS_ENTRY:=127.0.0.1 dashboard.localhost sales.localhost purchase.localhost catalog.localhost

## hosts-entry: Set up an entry for this project's host names in /etc/hosts
.PHONY: hosts-entry
hosts-entry:
	(grep "$(HOSTS_ENTRY)" /etc/hosts) || \ echo '$(HOSTS_ENTRY)' | sudo tee -a /etc/hosts

## up: Start all services for this project
up: hosts-entry
	docker-compose up -d --force-recreate --no-build --remove-orphans

## down: Stop and remove all containers and volumes for this project
.PHONY: down
down:
	docker-compose down --remove-orphans -v

build:
	docker build -t matthiasnoback/php_workshop_tools_templated_nginx:latest -f docker/nginx/Dockerfile docker/nginx/
	docker build -t matthiasnoback/php_workshop_tools_php_fpm:latest -f docker/php-fpm/Dockerfile docker/php-fpm/

push: build
	docker push matthiasnoback/php_workshop_tools_templated_nginx:latest
	docker push matthiasnoback/php_workshop_tools_php_fpm:latest
