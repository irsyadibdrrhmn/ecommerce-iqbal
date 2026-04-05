web: vendor/bin/heroku-php-apache2 public/
release: npm run build && php artisan migrate --force
queue: php artisan queue:work --wait=3
scheduler: php artisan schedule:work
