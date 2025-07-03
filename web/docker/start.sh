#!/bin/bash
service cron start
php artisan octane:frankenphp --host=0.0.0.0 --port=8000 &
# Keep container alive, allowing cron to run
tail -f /dev/null