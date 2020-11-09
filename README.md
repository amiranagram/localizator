# Localizator

![Tests](https://github.com/amiranagram/localizator/workflows/Tests/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/amirami/localizator.svg)](https://packagist.org/packages/amirami/localizator)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/amirami/localizator.svg)](https://packagist.org/packages/amirami/localizator)

Localizator is a small tool for Laravel that gives you the ability to extract untranslated string from your project files with one command. Current version extracts only to JSON
 language files.

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
return [

    /**
     * Search criteria for files.
     */
    'search' => [
        /**
         * Directories which should be looked inside.
         */
        'dirs'      => ['resources/views'],

        /**
         * Patterns by which files should be queried.
         * The values can be a regular expresion, glob, or just a string.
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
    'sort'   => true,

];
```

## Usage

To extract all the strings, it's as simple as running:

``` bash
php artisan localize de,fr
```

This commands will create (if don't exist) `de.json` and `fr.json` files inside the `resources/lang` directory.

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
