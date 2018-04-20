#!/usr/bin/env bash

set -eu

vendor/bin/phpstan analyze --level=max src test
vendor/bin/phpunit --testsuite unit -vvv --stop-on-error
vendor/bin/phpunit --testsuite integration -vvv --stop-on-error
vendor/bin/behat --suite system -vvv --stop-on-failure
