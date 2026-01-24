#!/bin/bash
cd /home/pitchburnersadmin/htdocs/pitchburners.com
php artisan queue:work --sleep=3 --tries=3 --timeout=90

