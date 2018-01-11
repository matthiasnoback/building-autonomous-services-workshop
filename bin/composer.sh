#!/usr/bin/env bash

set -eux

DEFAULT_COMPOSER_HOME=~/.composer
COMPOSER_HOME=${COMPOSER_HOME-${DEFAULT_COMPOSER_HOME}}

docker run --rm --interactive --tty --volume "${PWD}:/app" --volume "${COMPOSER_HOME}:/tmp" --user "${HOST_UID}:${HOST_GID}" composer:latest $@
