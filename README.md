# Deployed

## Installation

You can install the package via composer:

```bash
composer require adamgaskins/deployed
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="AdamGaskins\Deployed\DeployedServiceProvider" --tag="deployed-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$skeleton = new AdamGaskins\Deployed();
echo $skeleton->echoPhrase('Hello, AdamGaskins!');
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
