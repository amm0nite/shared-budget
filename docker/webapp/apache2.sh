#!/bin/bash
mkdir -p /var/lock/apache2
chown www-data:www-data /var/lock/apache2
source /etc/apache2/envvars
apache2ctl -DFOREGROUND
