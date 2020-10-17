#!/bin/bash
FQ=( $RIV_FQ_PROXY )
sudo rm -vrf ./vendor/shunf4/graby-site-config
sudo rm -vrf ./var/cache/prod
env "${FQ[@]}" SYMFONY_ENV=prod composer install -o --no-dev
./fix_permissions.sh
