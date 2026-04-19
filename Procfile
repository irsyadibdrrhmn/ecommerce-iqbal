web: php artisan migrate --force && php artisan storage:link || true && php artisan serve --host=0.0.0.0 --port=${PORT}
queue: php artisan queue:work --tries=3 --timeout=90
scheduler: php artisan schedule:work
