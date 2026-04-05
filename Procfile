web: php artisan migrate --force && php artisan storage:link && php -S 0.0.0.0:8080 -t public
release: npm run build
queue: php artisan queue:work --wait=3
scheduler: php artisan schedule:work
