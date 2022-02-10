<?php

namespace Overtrue\LaravelPassportCacheToken;

use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Repository;
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
    protected $cacheKeyPrefix;

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
     * @param string|null $cacheKeyPrefix
     * @param int|null    $expiresInSeconds
     * @param array       $tags
     * @param string|null $store
     */
    public function __construct(string $cacheKeyPrefix = null, int $expiresInSeconds = null, array $tags = [], ?string $store = null)
    {
        $this->cacheKeyPrefix = sprintf('%s_token_', $cacheKeyPrefix ?? 'passport');
        $this->expiresInSeconds = $expiresInSeconds ?? 5 * 60;
        $this->cacheTags = $tags;
        $this->cacheStore = $store ?? \config('cache.default');
    }

    /**
     * Get a token by the given ID.
     *
     * @param string $id
     *
     * @return \Laravel\Passport\Token
     */
    public function find($id): ?Token
    {
        return $this->cacheStore()->remember(
            $this->itemKey($id),
            \now()->addSeconds($this->expiresInSeconds),
            function () use ($id) {
                return Passport::token()->where('id', $id)->first();
            }
        );
    }

    /**
     * Get a token by the given user ID and token ID.
     *
     * @param string $id
     * @param int    $userId
     *
     * @return \Laravel\Passport\Token|null
     */
    public function findForUser($id, $userId): ?Token
    {
        return $this->cacheStore()->remember(
            $this->itemKey($id),
            \now()->addSeconds($this->expiresInSeconds),
            function () use ($id, $userId) {
                return Passport::token()->where('id', $id)->where('user_id', $userId)->first();
            }
        );
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
        return $this->cacheStore()->remember(
            $this->itemKey($userId),
            \now()->addSeconds($this->expiresInSeconds),
            function () use ($userId) {
                return Passport::token()->where('user_id', $userId)->get();
            }
        );
    }

    /**
     * Get a valid token instance for the given user and client.
     *
     * @param \Illuminate\Database\Eloquent\Model $user
     * @param \Laravel\Passport\Client            $client
     *
     * @return \Laravel\Passport\Token|null
     */
    public function getValidToken($user, $client): ?Token
    {
        return $this->cacheStore()->remember(
            $this->itemKey($user->getKey()),
            \now()->addSeconds($this->expiresInSeconds),
            function () use ($client, $user) {
                return $client->tokens()
                    ->whereUserId($user->getKey())
                    ->where('revoked', 0)
                    ->where('expires_at', '>', \now())
                    ->first();
            }
        );
    }

    public function itemKey(string $key): string
    {
        return $this->cacheKeyPrefix . $key;
    }

    public function cacheStore(): Repository
    {
        $store = Cache::store($this->cacheStore);

        return $store->getStore() instanceof TaggableStore ? $store->tags($this->cacheTags) : $store;
    }

    public function revokeAccessToken($id)
    {
        parent::revokeAccessToken($id);

        $this->cacheStore()->forget($this->itemKey($id));
    }
}
