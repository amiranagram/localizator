# Localizator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/amirami/localizator.svg?style=flat-square)](https://packagist.org/packages/amirami/localizator)
![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/amirami/localizator/run-tests?label=tests)
[![Total Downloads](https://img.shields.io/packagist/dt/amirami/localizator.svg?style=flat-square)](https://packagist.org/packages/amirami/localizator)

## Installation

You can install the package via composer:

```bash
composer require amirami/localizator
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Amirami\Localizator\ServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [

    'sort' => true,

    'search' => [
        'dirs'     => ['resources/views'],
        'patterns' => ['*.php'],
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
