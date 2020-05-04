<?php

namespace Nerdzlab\LaravelSocialiteAppleSignIn;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Nerdzlab\LaravelSocialiteAppleSignIn\Exceptions\AppleSignInException;
use Throwable;

class SignInWithAppleProvider extends AbstractProvider implements ProviderInterface
{
    private const VERIFICATION_ALGORITHM = 'RS256';
    private const TOKEN_ISSUER = 'https://appleid.apple.com';

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
        return $this->validateToken($token);
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


    private function validateToken(string $token): array
    {
        $tokenData = $this->checkTokenSignature($token);

        if ($this->clientId !== $tokenData['aud']) {
            throw AppleSignInException::invalidClientId();
        }

        if (self::TOKEN_ISSUER !== $tokenData['iss']) {
            throw AppleSignInException::invalidIssuer();
        }

        return $tokenData;
    }

    private function checkTokenSignature(string $token): array
    {
        $keyId = $this->extractKeyId($token);

        $this->prepareKey($keyId);

        try {
            $payload = JWT::decode(
                $token,
                $this->keyStorage()->get($this->keyPath($keyId)),
                [self::VERIFICATION_ALGORITHM]
            );
        } catch (Throwable $exception) {
            throw AppleSignInException::invalidToken($exception);
        }

        return (array)$payload;
    }

    private function extractKeyId(string $token): string
    {
        $header = json_decode(base64_decode(explode('.', $token)[0]), true);

        if (!$kid = (string)Arr::get($header, 'kid')) {
            throw AppleSignInException::invalidPublicKeyId();
        }

        return $kid;
    }

    private function getAuthKeys(): array
    {
        $response = $this->getHttpClient()->get('https://appleid.apple.com/auth/keys');

        return json_decode($response->getBody(), true);
    }

    private function prepareKey(string $keyId): void
    {
        if (!$this->keyStorage()->get($this->keyPath($keyId))) {
            $this->updatePublicKeys();
        }

        if (!$this->keyStorage()->get($this->keyPath($keyId))) {
            throw AppleSignInException::invalidPublicKeyId();
        }
    }

    private function updatePublicKeys(): void
    {
        $publicKeys = JWK::parseKeySet($this->getAuthKeys());

        foreach ($publicKeys as $name => $resource) {
            $keyData = openssl_pkey_get_details($resource);

            $this->keyStorage()->add($this->keyPath($name), $keyData['key'], config('socialite-apple.cache.ttl'));
        }
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
