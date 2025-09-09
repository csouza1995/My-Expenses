#!/usr/bin/env bash
set -e

# Corrigir permissões
chown -R www-data:www-data /var/www/html/runtime /var/www/html/web/assets
chmod -R 775 /var/www/html/runtime /var/www/html/web/assets

exec "$@"