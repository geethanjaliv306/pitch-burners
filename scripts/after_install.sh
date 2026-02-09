#!/bin/bash
set -e

cd /var/www/html/pitchburners
mkdir -p test
chown -R apache:apache /var/www/html/pitchburners
chmod -R 775 /var/www/html/pitchburners
sudo systemctl restart httpd
#Code Deploy Check