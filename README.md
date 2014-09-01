# Laravel stored procedures
*Only works with PostgreSQL for now.*

## Installation
Require this package by adding following dependency on your composer.json
`"mrblackus/laravel-storedprocedures": "dev-master"`

Update composer with `composer update` then add the ServiceProvider on app/config/app.php :
`'Mrblackus\LaravelStoredprocedures\LaravelStoredproceduresServiceProvider'`

## Usage
You can generate model for your stored procedures (aka *functions*) by typing this command
`php artisan generate-sp`
Models will be written in app/models/ directory. **Do not edit these models !** They will be overwritten at next generation.