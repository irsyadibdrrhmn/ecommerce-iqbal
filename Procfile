web: php artisan migrate --force && php artisan storage:link || true && php -S 0.0.0.0:${PORT} -t public
release: npm ci && npm run build
queue: php artisan queue:work --tries=3 --timeout=90
scheduler: php artisan schedule:work
