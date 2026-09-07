#!/bin/bash
set -e

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
  echo "Installing PHP dependencies..."
  composer install --no-interaction --prefer-dist
fi

if [ ! -f .env ]; then
  echo "No .env found; copying .env.example"
  cp .env.example .env
fi

# Generate APP_KEY without booting Laravel (service providers query MySQL).
if grep -qE '^APP_KEY=\s*$' .env; then
  KEY="base64:$(head -c 32 /dev/urandom | base64 | tr -d '\n')"
  sed -i "s|^APP_KEY=.*|APP_KEY=${KEY}|" .env
  echo "Generated APP_KEY"
fi

# Same as `php artisan storage:link`, without booting the framework.
# On Windows bind mounts this path is often already a junction/directory, or
# ln cannot create a Unix symlink — never fail container startup over it.
if [ -e public/storage ] || [ -h public/storage ]; then
  :
else
  ln -sfn ../storage/app/public public/storage 2>/dev/null || \
    echo "Warning: could not create public/storage link (common on Windows Docker)."
fi

# Writable dirs Laravel needs at runtime. On Windows bind mounts chown may no-op.
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

exec "$@"
