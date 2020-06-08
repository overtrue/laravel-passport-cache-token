<?php

namespace Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;
use Laravel\Passport\PassportServiceProvider;
use Overtrue\LaravelPassportCacheToken\CacheTokenServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    public const KEYS = __DIR__ . '/keys';
    public const PUBLIC_KEY = self::KEYS . '/oauth-public.key';
    public const PRIVATE_KEY = self::KEYS . '/oauth-private.key';

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh');

        Passport::routes();

        @unlink(self::PUBLIC_KEY);
        @unlink(self::PRIVATE_KEY);

        $this->artisan('passport:keys');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    protected function getEnvironmentSetUp($app)
    {
        $config = $app->make(Repository::class);

        $config->set('auth.defaults.provider', 'users');

        if (($userClass = $this->getUserClass()) !== null) {
            $config->set('auth.providers.users.model', $userClass);
        }

        $config->set('auth.guards.api', ['driver' => 'passport', 'provider' => 'users']);

        $app['config']->set('database.default', 'testbench');

        $app['config']->set('passport.storage.database.connection', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('app.key', 'base64:L7o69H49J7GM4qVX/lfI2DOPW8fqCfw+kiahKsxL62g=');
    }

    protected function getPackageProviders($app)
    {
        return [PassportServiceProvider::class, CacheTokenServiceProvider::class];
    }

    /**
     * Get the Eloquent user model class name.
     *
     * @return string|null
     */
    protected function getUserClass()
    {
        return User::class;
    }
}
