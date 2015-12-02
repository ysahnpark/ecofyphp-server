# Migrations

## DB Migration

### Generating migration script
php artisan make:migration create_accounts_table --create=accounts

### Running migration
php artisan migrate


## Models (Eloquent)

php artisan make:model Account

## recompile
php artisan clear-compiled
