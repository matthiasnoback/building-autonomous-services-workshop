#!/usr/bin/env bash

set -e

git add .
filename=$(date +%s).patch
git diff --staged > ${filename}
git reset HEAD

echo -e "Now, send \033[0;32m${filename}\033[0m to matthiasnoback@gmail.com!"
<<<<<<< HEAD
echo -e "Run \033[0;33mgit clean -f\033[0m to remove new untracked files you have created."
=======
>>>>>>> 9355ab1367072dcf6d0fe106803140b3aa1923b0
