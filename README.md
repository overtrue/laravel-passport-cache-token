<h1 align="center">Laravel Passport Cache Token</h1>

<p align="center"> Make laravel/passport token cacheable..</p>


## Installing

```shell
$ composer require overtrue/laravel-passport-cache-token -vvv
```

## Usage

Thanks to Laravel's automatic package discovery mechanism, you don't need to do any additional operations.

Of course, you can also control the cache strategy freely, just need to configure the following in the configuration file:

**config/passport.php**
```php
return [
    //...
    'cache' => [
        // Cache key prefix
        'prefix' => 'passport_',
        
        // The lifetime of token cache,
        // Unit: second
        'expires_in' => 300,
        
        // Cache tags
        'tags' => [],
    ],
];
```

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/overtrue/laravel-passport-cache-token/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/overtrue/laravel-passport-cache-token/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT
