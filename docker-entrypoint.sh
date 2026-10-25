#!/bin/bash
set -e

PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Ensure storage directories exist and are writable
mkdir -p /var/www/html/storage/{evidence,logs,samples}
chown -R www-data:www-data /var/www/html/storage

exec apache2-foreground
