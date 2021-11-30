# Laravel Passport Cache Token

Make [laravel/passport](https://github.com/laravel/passport) token cacheable.

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me-button-s.svg?raw=true)](https://github.com/sponsors/overtrue)


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


## :heart: Sponsor me 

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me.svg?raw=true)](https://github.com/sponsors/overtrue)

如果你喜欢我的项目并想支持它，[点击这里 :heart:](https://github.com/sponsors/overtrue)


## Project supported by JetBrains

Many thanks to Jetbrains for kindly providing a license for me to work on this and other open-source projects.

[![](https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg)](https://www.jetbrains.com/?from=https://github.com/overtrue)

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/overtrue/laravel-passport-cache-token/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/overtrue/laravel-passport-cache-token/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT
