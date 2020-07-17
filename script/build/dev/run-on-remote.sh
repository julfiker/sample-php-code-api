/home/ubuntu/.bin/composer install --no-dev -d /var/www/spoly-api/
php /var/www/spoly-api/artisan migrate
php /var/www/spoly-api/artisan cache:clear