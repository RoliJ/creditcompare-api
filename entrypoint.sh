#!/bin/sh
set -e

# If the vendor folder is missing or you want to always ensure dependencies are installed, run composer install
if [ ! -d "vendor" ]; then
  echo "Installing composer dependencies..."
  composer install --no-interaction --optimize-autoloader
fi

# Optionally, you can always run composer install to ensure dependencies are updated
# echo "Installing/updating composer dependencies..."
# composer install --no-interaction --optimize-autoloader

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
