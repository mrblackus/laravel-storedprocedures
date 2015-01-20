[![Build Status](https://travis-ci.org/mrblackus/laravel-storedprocedures.svg?branch=master)](https://travis-ci.org/mrblackus/laravel-storedprocedures)
# Laravel stored procedures
This package allow you to work with PostgreSQL stored procedures with Laravel 4. It allows you to generate models to simply use your procedures in your PHP code.

## Installation

### Package installation
#### With Composer
Require this package by adding following dependency on your composer.json
```javascript
"mrblackus/laravel-storedprocedures": "dev-master"
```
Then update composer with `composer update` or `composer install`.

#### With Laravel bundle
If you do not want to use Composer, you can install it by using Laravel Bundle by typing this command
```
php artisan bundle:install StoredProcedure
```
### Registering service provider
Once the package is installed, add the ServiceProvider in `providers` array on app/config/app.php :
```php
'providers' => array(
    'Mrblackus\LaravelStoredprocedures\LaravelStoredproceduresServiceProvider'
);
```

## Usage
You can generate model for your stored procedures (aka *functions*) by typing this command
```
php artisan generate-sp
```
**Only stored procedures which name start with ``sp_`` will have a model generated, other ones will be ignored.**
Models will be written in app/store_procedures directory (or the one defined in configuration file). **Do not edit these models !** They will be overwritten at next generation.

## Configuration
You can override default configuration by publishing configuration file and editing it.
```
php artisan config:publish mrblackus/laravel-storedprocedures
```
You can change the database schema to read and the directory where models for stored procedures are written.

```php
return array(
    'schema'         => 'public',
    'model_save_dir' => 'app/stored_procedures'
);
```

## Models
Generated models have an `execute()` methods that allow you to execute stored procedure and get result (if the procedure returns one) from it.

If procedure have IN or INOUT parameter, the `execute()` method will have the same parameter in the same order.

If it has OUT or INOUT parameter, model will have an attribute with getter/setter for every OUT parameters.

**Setters will not modify data**, they are only present to allow you to format data if you want to use it on views by uysing whole model instead of simple variables.

## Example
Giving the following stored procedure, that retrieve all friends of a user giving its id...
```sql
CREATE OR REPLACE FUNCTION sp_getfriends(IN id integer, OUT id integer, OUT username character varying, OUT firstname character varying, OUT lastname character varying, OUT facebook_id character varying)
  RETURNS SETOF record AS
$BODY$SELECT id, username, firstname, lastname, facebook_id
FROM users
LEFT JOIN friends ON users.id = friends.friend_with_id
WHERE friends.user_id = $1$BODY$
  LANGUAGE sql VOLATILE
  COST 100
  ROWS 1000;
```
...we will have a `SP_Getfriends` class generated. We can use it like this :
```php
$userId = 20;
$friends = SP_Getfriends::execute($userId);

$firstFriend = $friends[0];
$friend->getId();
$friend->getUsername();
$friend->getFacebookid();
```
