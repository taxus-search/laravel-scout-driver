# laravel Scout Taxus Driver


This package makes is the [Taxus](https://taxus.ir) driver for Laravel Scout.

## Contents

- [Requirements](#Requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Credits](#credits)

## Requirements
This package is based on [laravel scout](https://laravel.com/docs/5.6/scout) package, So please install scout v.4 before using taxus search engine.

## Installation

You can install the package via composer:

``` bash
composer require taxus-search/laravel-scout-driver
```

You must add the Scout service provider and the package service provider in your app.php config:

```php
// config/app.php
'providers' => [
    ...
    Laravel\Scout\ScoutServiceProvider::class,
    ...
    TaxusSearch\LaravelScoutDriver\LaravelScoutDriverProvider::class,

],
```

### Setting up Taxus configuration
At first, you should change your driver in config/scout.php file or in the environments:
```php
// config/scout.php
// Set your driver to taxus
    'driver' => env('SCOUT_DRIVER', 'taxus'),
```

You must have an api key and a search api key. visit [Taxus.ir](https://taxus.ir)

If you need help with this please refer to the [Taxus documentation](https://taxus.ir/docs)

After you've published the Laravel Scout package configuration, you should publish taxus:

```php
php artisan vendor:publish
```
After that, you can see taxus.php in the config folder
```php
// config/taxus.php
    'key' => env('TAXUS_API_KEY', 'Taxus_API_KEY'),
    'search_key' => env('TAXUS_SEARCH_API_KEY', 'XXXXXXXXXXXXXXXX'),

```

## Usage

Now you can use Laravel Scout as described in the [official documentation](https://laravel.com/docs/5.3/scout)
## Credits

- [taxus.ir](https://taxus.ir)
