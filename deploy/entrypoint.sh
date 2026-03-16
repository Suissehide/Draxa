#!/bin/sh
set -e

php bin/console cache:clear --no-debug
php bin/console assets:install --no-debug
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

exec "$@"
