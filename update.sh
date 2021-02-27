#!/bin/bash
set -eo pipefail

FQ=( $RIV_FQ_PROXY )
sudo rm -vrf ./vendor/shunf4/graby-site-config
sudo rm -vrf ./var/cache/prod
env "${FQ[@]}" COMPOSER_MEMORY_LIMIT=-1 SYMFONY_ENV=prod ENV=prod composer install -o --no-dev
./fix_permissions.sh
