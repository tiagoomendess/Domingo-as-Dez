#!/bin/bash
set -e

cd /var/www/html

mkdir -p \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  bootstrap/cache \
  vendor

# Prefer the vendor tree baked into the image. It lives on the Linux volume,
# which is much faster to stat than the Windows project mount.
if [ ! -f vendor/autoload.php ] && [ -d /opt/vendor ]; then
  echo "Seeding vendor onto the Linux volume..."
  cp -a /opt/vendor/. vendor/
fi

LOCK=""
if [ -f composer.lock ]; then
  LOCK=$(md5sum composer.lock | awk '{print $1}')
fi

if [ ! -f vendor/autoload.php ] || { [ -n "$LOCK" ] && [ "$(cat vendor/.lock-hash 2>/dev/null || true)" != "$LOCK" ]; }; then
  echo "Installing PHP dependencies..."
  composer install --no-interaction --prefer-dist --no-scripts
  if [ -n "$LOCK" ]; then
    echo "$LOCK" > vendor/.lock-hash
  fi
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

# Only the Linux volume and the small cache dir. A recursive chmod of
# storage/ walks every uploaded file through the Windows mount and can
# block container startup for minutes.
chown -R www-data:www-data storage/framework bootstrap/cache 2>/dev/null || true
chmod -R ug+rwx storage/framework storage/logs bootstrap/cache 2>/dev/null || true

exec "$@"
