#!/bin/sh
set -e

echo "Running storage link..."
php artisan storage:link --force || true

echo "Clearing config..."
php artisan config:clear

echo "Running migrations..."
php artisan migrate --force

echo "Setting up queue table..."
php artisan queue:table 2>/dev/null || true
php artisan migrate --force

echo "Starting queue worker in background..."
php artisan queue:work --tries=3 --sleep=3 --timeout=60 &

echo "Starting server on port ${PORT:-8080}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
