# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/achetibi/laravel-satim.svg?style=flat-square)](https://packagist.org/packages/achetibi/laravel-satim)
[![Total Downloads](https://img.shields.io/packagist/dt/achetibi/laravel-satim.svg?style=flat-square)](https://packagist.org/packages/achetibi/laravel-satim)

Laravel Satim is a clean, extensible Laravel package that provides seamless integration with the Satim online payment gateway. It allows you to handle key payment operations like registering transactions, confirming payments, and processing refunds using a simple and robust service layer.

## Features

- Easy configuration via `.env` for credentials and endpoints
- Supports Satim’s full payment flow: register, confirm, and refund
- Built-in HTTP client abstraction and exception handling
- Framework-agnostic service logic wrapped in Laravel-friendly facades and service providers

## Installation

You can install the package via composer:

```bash
composer require achetibi/laravel-satim
```

## Usage

```php
php artisan vendor:publish --provider=LaravelSatim\SatimServiceProvider
```

You can configure the following environment variables:

```
SATIM_USERNAME=
SATIM_PASSWORD=
SATIM_TERMINAL=
SATIM_TIMEOUT=30
SATIM_LANGUAGE=en
SATIM_CURRENCY=DZD
SATIM_API_URL=
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

### Todo

- Integrate tests

### Security

If you discover any security related issues, please email chetibi.abderrahim@gmail.com instead of using the issue tracker.

## Credits

-   [Abderrahim CHETIBI](https://github.com/achetibi)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
