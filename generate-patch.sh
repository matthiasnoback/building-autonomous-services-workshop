#!/usr/bin/env bash

set -e

git add .
filename=$(date +%s).patch
git diff --staged > ${filename}
git reset HEAD

echo -e "Now, send \033[0;32m${filename}\033[0m to matthiasnoback@gmail.com!"
echo -e "Run \033[0;33mgit clean -f\033[0m to remove new untracked files you have created."
