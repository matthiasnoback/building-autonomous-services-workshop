#!/usr/bin/env bash

set -eu

vendor/bin/phpstan analyze --level=max src test
vendor/bin/phpunit --testsuite unit -v --stop-on-error
vendor/bin/behat --suite system -v --stop-on-failure
