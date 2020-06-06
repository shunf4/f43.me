#!/bin/bash

php7.2 /srv/f43.me/bin/console feed:fetch-items --env=prod new & tail -f /srv/f43.me/var/logs/prod.log

wait $(jobs -p)

