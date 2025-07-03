#!/bin/bash

# Export all current env vars to /etc/environment for cron
printenv | grep -v "no_proxy" > /etc/environment

# Start cron (if not already running)
service cron start
php artisan octane:frankenphp --host=0.0.0.0 --port=8000 &

# Keep container alive, allowing cron to run
tail -f /dev/null