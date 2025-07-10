#!/bin/sh

cd /var/www

# Install dependencies if not already installed
if [ ! -d "vendor" ]; then
  composer install
fi

php artisan serve --host=0.0.0.0 --port=8000
