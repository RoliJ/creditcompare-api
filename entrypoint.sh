#!/bin/sh
set -e

# Run migrations
echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

# Load fixtures
echo "Loading fixtures..."
php bin/console doctrine:fixtures:load --no-interaction

# Import credit card data into the database
echo "Importing credit card data into the database..."
php bin/console app:import-creditcards --no-interaction

# Start Apache server
echo "Starting Apache..."
exec apache2-foreground
