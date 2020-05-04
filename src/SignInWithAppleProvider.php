<?php

namespace Nerdzlab\LaravelSocialiteAppleSignIn;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Nerdzlab\LaravelSocialiteAppleSignIn\Exceptions\AppleSignInException;
use Throwable;

class SignInWithAppleProvider extends AbstractProvider implements ProviderInterface
{
    private const ALGORITHM = 'RS256';
    private const TOKEN_ISSUER = 'https://appleid.apple.com';
    private const JWK_URL = 'https://appleid.apple.com/auth/keys';

    protected function getAuthUrl($state): string
    {
        return '';
    }

    protected function getTokenUrl(): string
    {
        return '';
    }

    protected function getUserByToken($token): array
    {
        $tokenData = $this->decode($token);

        if ($this->clientId !== $tokenData['aud']) {
            throw AppleSignInException::invalidClientId();
        }

        if (self::TOKEN_ISSUER !== $tokenData['iss']) {
            throw AppleSignInException::invalidIssuer();
        }

        return $tokenData;
    }

    protected function mapUserToObject(array $user): User
    {
        return (new User)
            ->setRaw($user)
            ->map([
                'id'    => $user['sub'],
                'email' => $user['email'] && !array_key_exists('is_private_email', $user) ? $user['email'] : null,
            ]);
    }

    private function decode(string $token): array
    {
        try {
            $payload = JWT::decode(
                $token,
                $this->getPublicKey($this->extractKid($token)),
                [self::ALGORITHM]
            );
        } catch (Throwable $exception) {
            throw AppleSignInException::invalidToken($exception);
        }

        return (array)$payload;
    }

    private function extractKid(string $token): string
    {
        $header = JWT::jsonDecode(JWT::urlsafeB64Decode(Str::before($token, '.')));

        if (!$header || !isset($header->kid) || !$kid = (string)$header->kid) {
            throw AppleSignInException::invalidKid();
        }

        return $kid;
    }

    public function getJWK(string $keyId): string
    {
        $body = $this->getHttpClient()->get(self::JWK_URL)->getBody();

        $parsed = JWK::parseKeySet(json_decode($body, true));

        if (!$key = data_get($parsed, $keyId)) {
            throw AppleSignInException::invalidKid();
        }

        return openssl_pkey_get_details($key)['key'];
    }

    private function getPublicKey(string $keyId): string
    {
        return $this->keyStorage()->remember(
            $this->keyPath($keyId),
            config('socialite-apple.cache.ttl'),
            function () use ($keyId) {
                return $this->getJWK($keyId);
            }
        );
    }

    private function keyPath(string $name): string
    {
        return config('socialite-apple.cache.prefix') . $name;
    }

    private function keyStorage(): Repository
    {
        return Cache::store(config('socialite-apple.cache.store'));
    }
}
