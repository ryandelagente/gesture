#!/usr/bin/env bash
#
# deploy.sh — Cloudways post-pull deployment for Gesture (Laravel)
#
# Cloudways "Deployment via GIT" only PULLS code. It does not install
# dependencies, build, migrate, or cache. Run this script once after each Pull:
#
#     cd applications/*/public_html
#     bash deploy.sh
#
# It is idempotent — safe to run again any time.
#
set -euo pipefail

cd "$(dirname "$0")"
echo "==> Deploying Gesture from: $(pwd)"

# 1. PHP dependencies (production, no dev packages)
echo "==> [1/8] composer install"
COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# 1b. Ensure Laravel's writable storage skeleton exists.
# storage/framework is gitignored, so a fresh clone/deploy has no cache/views dirs
# and Blade compilation fails with "Please provide a valid cache path."
echo "==> [2/8] ensuring storage + bootstrap/cache directories"
mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/testing \
    storage/logs \
    storage/app/public \
    bootstrap/cache
chmod -R ug+rw storage bootstrap/cache 2>/dev/null || true

# 2. First-run env bootstrap — never overwrites an existing .env
if [ ! -f .env ]; then
    echo "==> [3/8] .env not found — creating from .env.example"
    cp .env.example .env
    php artisan key:generate --force
    echo ""
    echo "  ===================================================================="
    echo "  FIRST RUN: .env was just created. Now edit it with real values:"
    echo "      nano .env"
    echo "    - DB_DATABASE / DB_USERNAME / DB_PASSWORD  (from Cloudways Access Details)"
    echo "    - APP_URL=https://tracker.wehelptradies.com.au"
    echo "    - APP_ENV=production   APP_DEBUG=false"
    echo "    - GOOGLE_APPLICATION_CREDENTIALS  (path to service-account JSON, outside public_html)"
    echo "  Then run:  bash deploy.sh   again to migrate + cache."
    echo "  ===================================================================="
    exit 0
fi

# 3. Generate an app key if one is somehow missing (encrypted fields depend on it)
if ! grep -q '^APP_KEY=base64:' .env; then
    echo "==> [4/8] APP_KEY missing — generating"
    php artisan key:generate --force
else
    echo "==> [4/8] APP_KEY present — keeping existing key"
fi

# 4. Public storage symlink (idempotent)
echo "==> [5/8] storage:link"
php artisan storage:link 2>/dev/null || true

# 5. Database migrations (non-interactive)
echo "==> [6/8] migrate --force"
php artisan migrate --force

# 6. Writable permissions for runtime dirs
echo "==> [7/8] fixing storage / bootstrap cache permissions"
chmod -R ug+rw storage bootstrap/cache 2>/dev/null || true

# 7. Rebuild framework caches (clear stale config first, then optimize)
echo "==> [8/8] rebuilding caches"
php artisan optimize:clear
php artisan optimize

echo ""
echo "==> DONE."
echo "    If this is the first deploy, set Cloudways App Settings -> Webroot to:"
echo "        public_html/public"
echo "    and add a cron (Cron Job Management):"
echo "        * * * * * php $(pwd)/artisan schedule:run >> /dev/null 2>&1"
