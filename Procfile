web: frankenphp run --listen=:8080
release: npm run build && php artisan migrate --force && php artisan storage:link
queue: php artisan queue:work --wait=3
scheduler: php artisan schedule:work
