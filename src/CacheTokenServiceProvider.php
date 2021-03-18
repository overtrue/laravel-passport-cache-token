<?php

namespace Overtrue\LaravelPassportCacheToken;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\TokenRepository;

class CacheTokenServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(TokenRepository::class, function () {
            return new CacheTokenRepository(
                \config('passport.cache.prefix'),
                \config('passport.cache.expires_in'),
                \config('passport.cache.tags', []),
                \config('passport.cache.store', \config('cache.default'))
            );
        });
    }
}
