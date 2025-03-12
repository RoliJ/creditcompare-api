#!/bin/sh
set -e

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Load fixtures
php bin/console doctrine:fixtures:load --no-interaction

# Start Apache server
exec apache2-foreground