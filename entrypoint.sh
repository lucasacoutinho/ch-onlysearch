#!/bin/bash

cd /var/www/html/app/

php artisan migrate --force >> /var/log/laravel.log

php artisan scribe:generate >> /var/log/laravel.log
