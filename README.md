# Localizator

![Tests](https://github.com/amiranagram/localizator/workflows/Tests/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/amirami/localizator.svg)](https://packagist.org/packages/amirami/localizator)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/amirami/localizator.svg)](https://packagist.org/packages/amirami/localizator)

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

    'sort' => true,

    'search' => [
        'dirs'      => ['resources'],
        'patterns'  => ['*.php'],
        'functions' => ['__', 'trans', '@lang']
    ],

];
```

## Usage

``` bash
php artisan localize en,de
```

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
