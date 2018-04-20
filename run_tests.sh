#!/usr/bin/env bash

set -eu

vendor/bin/phpstan analyze --level=max src test
vendor/bin/behat --suite system -vvv
