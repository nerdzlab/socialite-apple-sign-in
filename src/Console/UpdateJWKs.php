<?php

namespace Nerdzlab\SocialiteAppleSignIn\Console;

use Firebase\JWT\JWK;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Nerdzlab\SocialiteAppleSignIn\SignInWithAppleProvider;
use Throwable;

class UpdateJWKs extends Command
{
    protected $signature = 'apple-sign-in:update-keys';

    protected $description = 'Update jwk and store public keys in cache.';

    public function handle(Client $client): void
    {
        try {
            $body = $client->get(SignInWithAppleProvider::JWK_URL)->getBody();

            $parsed = JWK::parseKeySet(json_decode($body, true));

            foreach ($parsed as $kid => $resource) {
                $this->storeKey($kid, $resource);
            }
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());
            $this->error($exception->getTraceAsString());
        }

        $this->info('All keys successfully updated.');
    }

    private function storeKey(string $kid, $resource): void
    {
        Cache::store(config('apple_sign_in.cache.store'))->set(
            config('apple_sign_in.cache.prefix') . $kid,
            openssl_pkey_get_details($resource->getKeyMaterial())['key'],
            config('apple_sign_in.cache.ttl')
        );
    }
}
