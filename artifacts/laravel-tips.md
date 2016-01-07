# Migrations

## DB Migration

### Generating migration script
php artisan make:migration create_relations_table --create=relations

### Running migration
php artisan migrate

### Generating seeder
php artisan make:seeder AccountsTableSeeder

php artisan db:seed [--class=AccountsTableSeeder]

## Models (Eloquent)

php artisan make:model Account

## recompile
php artisan clear-compiled

## Laravel 5.1 tweak for date output as ISO8601
Laravel 5.1's Eloquent model JSON serialization logic converts Date into
the format as specified in $dateFormat property.
The problem is that the $dateFormat is used for MySQL but is is not the desired
format for REST API, (ISO8601 format).

To have the Model serialize to ISO8601, do the following tweak:
1. In /vendor/laravel/fraemwork/src/Illuminate/Database/Eloquent/Model.php
modify the implementation of the metnod
protected function serializeDate(DateTime $date)
to be:
$attributes[$key] = (string)$this->asDateTime($attributes[$key]);

2. In the /bootstrap/app.php add the following line
\Carbon\Carbon::setToStringFormat('c');
