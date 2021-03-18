<?php

namespace Overtrue\LaravelPassportCacheToken;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\Cache;
use Laravel\Passport\TokenRepository;

class CacheTokenRepository extends TokenRepository
{
    /**
     * @var string
     */
    protected $cacheKey;

    /**
     * @var int
     */
    protected $expiresInSeconds;

    /**
     * @var array
     */
    protected $cacheTags;

    /**
     * @var string
     */
    protected $cacheStore;

    /**
     * @param string $cacheKey
     * @param int    $expiresInSeconds
     * @param array  $tags
     */
    public function __construct(string $cacheKey = null, int $expiresInSeconds = null, array $tags = [], string $store = 'file')
    {
        $this->cacheKey = $cacheKey ?? 'passport_token_';
        $this->expiresInSeconds = $expiresInSeconds ?? 5 * 60;
        $this->cacheTags = $tags;
        $this->cacheStore = $store;
    }

    /**
     * Get a token by the given ID.
     *
     * @param string $id
     *
     * @return \Laravel\Passport\Token
     */
    public function find($id)
    {
        return Cache::store($this->cacheStore)->remember($this->cacheKey . $id, \now()->addSeconds($this->expiresInSeconds), function () use ($id) {
            return Passport::token()->where('id', $id)->first();
        });
    }

    /**
     * Get a token by the given user ID and token ID.
     *
     * @param string $id
     * @param int    $userId
     *
     * @return \Laravel\Passport\Token|null
     */
    public function findForUser($id, $userId)
    {
        return Cache::store($this->cacheStore)->remember($this->cacheKey . $id, \now()->addSeconds($this->expiresInSeconds), function () use ($id, $userId) {
            return Passport::token()->where('id', $id)->where('user_id', $userId)->first();
        });
    }

    /**
     * Get the token instances for the given user ID.
     *
     * @param mixed $userId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function forUser($userId): Collection
    {
        return Cache::store($this->cacheStore)->remember($this->cacheKey . $userId, \now()->addSeconds($this->expiresInSeconds), function () use ($userId) {
            return Passport::token()->where('user_id', $userId)->get();
        });
    }

    /**
     * Get a valid token instance for the given user and client.
     *
     * @param \Illuminate\Database\Eloquent\Model $user
     * @param \Laravel\Passport\Client            $client
     *
     * @return \Laravel\Passport\Token|null
     */
    public function getValidToken($user, $client)
    {
        return Cache::store($this->cacheStore)->remember($this->cacheKey . $user->getKey(), \now()->addSeconds($this->expiresInSeconds), function () use ($client, $user) {
            return $client->tokens()
                ->whereUserId($user->getKey())
                ->where('revoked', 0)
                ->where('expires_at', '>', \now())
                ->first();
        });
    }
}
