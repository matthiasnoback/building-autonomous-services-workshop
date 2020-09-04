#!/usr/bin/env bash

set -eu

vendor/bin/phpstan analyze
vendor/bin/phpunit -v --stop-on-error
vendor/bin/behat -v --stop-on-failure
