@servers(['production' => ['ubuntu@app.mailyser.io']])

@task('deploy-production', ['on' => 'production'])
cd /var/www/app.mailyser.io
git pull origin master
npm ci
npm run build
composer install --no-interaction --quiet --no-dev --prefer-dist --optimize-autoloader
php artisan migrate --force --no-interaction
php artisan optimize
php artisan horizon:terminate
@endtask
