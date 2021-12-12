# Handli json exceptions handler
Laravel json exception handling for web applications (forces json response in Laravel).

## Install
```sh
cd app

composer require breakermind/handli

composer update
```

## Handli edit config
```sh
php artisan vendor:publish --tag=handli-config --force

php artisan vendor:publish --provider="Handli\HandliServiceProvider.php"
```

## Config
config/handli.php
```php
return [
	'force_json_response' => true, // Default
	'debug' => false, // Default
];

 ```

## Install package without packagist
```json
{
	"repositories": [{
		"type": "vcs",
		"url": "https://github.com/breakermind/handli"
	}],
	"require": {
		"breakermind/handli": "^1.0"
	}
}
```

### Add service provider to config/app.php (if errors)
```php
'providers' => [
	Handli\HandliServiceProvider::class,
]
```
