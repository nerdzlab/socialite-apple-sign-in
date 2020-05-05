<?php

namespace Nerdzlab\LaravelSocialiteAppleSignIn\Console;

use Firebase\JWT\JWK;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Nerdzlab\LaravelSocialiteAppleSignIn\SignInWithAppleProvider;
use Throwable;

class UpdateJWKs extends Command
{
    protected $signature = 'apple-sign-in:update-jwks';

    protected $description = 'Update jwk and store public keys in cache.';

    public function handle(Client $client): void
    {
        try {
            $body = $client->get(SignInWithAppleProvider::JWK_URL)->getBody();

            $parsed = collect(JWK::parseKeySet(json_decode($body, true)));

            foreach ($parsed as $kid => $resource) {
                $this->storeKey($kid, $resource);
            }
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());
        }

        $this->info('All keys successfully updated.');
    }

    private function storeKey(string $kid, $resource): void
    {
        Cache::store(config('apple-sign-in.cache.store'))->set(
            config('apple-sign-in.cache.prefix') . $kid,
            openssl_pkey_get_details($resource)['key'],
            config('apple-sign-in.cache.ttl')
        );
    }
}
