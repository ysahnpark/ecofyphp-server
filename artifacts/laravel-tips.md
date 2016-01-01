# Migrations

## DB Migration

### Generating migration script
php artisan make:migration create_accounts_table --create=accounts

### Running migration
php artisan migrate

### Generating seeder
php artisan make:seeder AccountsTableSeeder

php artisan db:seed [--class=AccountsTableSeeder]

## Models (Eloquent)

php artisan make:model Account

## recompile
php artisan clear-compiled

## Laravel 5.1 hack for date output as ISO8601
In /vendor/laravel/fraemwork/src/Illuminate/Database/Eloquent/Model.php
modify the implementation of th emetnod
protected function serializeDate(DateTime $date)
to be:
$attributes[$key] = (string)$this->asDateTime($attributes[$key]);
And then in the /bootstrap/app.php add the following line
\Carbon\Carbon::setToStringFormat('c');
