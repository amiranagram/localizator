# Localizator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/amirami/localizator.svg?style=flat-square)](https://packagist.org/packages/amirami/localizator)
[![Total Downloads](https://img.shields.io/packagist/dt/amirami/localizator.svg?style=flat-square)](https://packagist.org/packages/amirami/localizator)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/amiranagram/localizator/tests?label=tests&style=flat-square)](https://github.com/amiranagram/localizator/actions?query=workflow%3Atests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/amiranagram/localizator/Check%20&%20fix%20styling?label=code%20style&style=flat-square)](https://github.com/amiranagram/localizator/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)

Localizator is a small tool for Laravel that gives you the ability to extract untranslated string from your project files with one command.

## Support

<a href="https://www.buymeacoffee.com/amirami" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/default-orange.png" alt="Buy Me A Coffee" height="41" width="174"></a>

[![DigitalOcean Referral Badge](https://web-platforms.sfo2.digitaloceanspaces.com/WWW/Badge%203.svg)](https://www.digitalocean.com/?refcode=f38828dd20f8&utm_campaign=Referral_Invite&utm_medium=Referral_Program&utm_source=badge)

## Installation

You can install the package via composer:

```bash
composer require --dev amirami/localizator
```

This package makes use of [Laravels package auto-discovery mechanism](https://medium.com/@taylorotwell/package-auto-discovery-in-laravel-5-5-ea9e3ab20518), which means if you don't install dev dependencies in production, it also won't be loaded.

If for some reason you want manually control this:
- add the package to the `extra.laravel.dont-discover` key in `composer.json`, e.g.
  ```json
  "extra": {
    "laravel": {
      "dont-discover": [
        "amirami/localizator"
      ]
    }
  }
  ```
- Add the following class to the `providers` array in `config/app.php`:
  ```php
  Amirami\Localizator\ServiceProvider::class,
  ```
  If you want to manually load it only in non-production environments, instead you can add this to your `AppServiceProvider` with the `register()` method:
  ```php
  public function register()
  {
      if ($this->app->isLocal()) {
          $this->app->register(\Amirami\Localizator\ServiceProvider::class);
      }
      // ...
  }
  ```

> Note: Avoid caching the configuration in your development environment, it may cause issues after installing this package; respectively clear the cache beforehand via `php artisan cache:clear` if you encounter problems when running the commands

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Amirami\Localizator\ServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
<?php

return [

    /**
     * Localize types of translation strings.
     */
    'localize' => [
        /**
         * Short keys. This is the default for Laravel.
         * They are stored in PHP files inside folders name by their locale code.
         * Laravel comes with default: auth.php, pagination.php, passwords.php and validation.php
         */
        'default' => true,

        /**
         * Translations strings as key.
         * They are stored in JSON file for each locale.
         */
        'json'    => true,
    ],

    /**
     * Search criteria for files.
     */
    'search'   => [
        /**
         * Directories which should be looked inside.
         */
        'dirs'      => ['resources/views'],

        /**
         * Patterns by which files should be queried.
         * The values can be a regular expression, glob, or just a string.
         */
        'patterns'  => ['*.php'],

        /**
         * Functions that the strings will be extracted from.
         * Add here any custom defined functions.
         * NOTE: The translation string should always be the first argument.
         */
        'functions' => ['__', 'trans', '@lang']
    ],

    /**
     * Should the localize command sort extracted strings alphabetically?
     */
    'sort'     => true,

];

```

## Usage

To extract all the strings, it's as simple as running:

``` bash
php artisan localize de,fr
```

This command will create (if don't exist) `de.json` and `fr.json` files inside the `resources/lang` directory.
If you have short keys enabled and used in your files (e.g. `pagination.next`) the localize command will create folders `de` and `fr` inside `resources/lang` directory and PHP files inside by the short key's prefix (e.g. `pagination.php`).

You can also run the artisan command without the country code arguments.

``` bash
php artisan localize
```

In this case translation strings will be generated for the language specified in `app.locale` config.

> Note: Strings you have already translated will not be overwritten.

### Key Sorting

By default, the strings generated inside those JSON files will be sorted alphabetically by their keys.
If you wanna turn off this feature just set `sort => false` in the config file.

### Searching

The way the strings are being extracted is simple.

We are looking inside the directories defined in `search.dirs` config, we match the files using patterns defined in `search.patterns`, and finally we look to extract strings
 which are the first argument of the functions defined in `search.functions`.
 
You are free to change any of these values inside the config file to suit you own needs.

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Amir Rami](https://github.com/amiranagram)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
