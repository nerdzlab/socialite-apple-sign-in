<?php

namespace Nerdzlab\SocialiteAppleSignIn;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Nerdzlab\SocialiteAppleSignIn\Exceptions\AppleSignInException;
use Throwable;

/**
 * @property array $clientId
 */
class SignInWithAppleProvider extends AbstractProvider implements ProviderInterface
{
    protected $stateless = true;

    private const ALGORITHM = 'RS256';
    private const TOKEN_ISSUER = 'https://appleid.apple.com';
    public const JWK_URL = 'https://appleid.apple.com/auth/keys';

    protected function getAuthUrl($state): string
    {
        return '';
    }

    protected function getTokenUrl(): string
    {
        return '';
    }

    /**
     * @param string $token
     * @return array
     * @throws \Nerdzlab\SocialiteAppleSignIn\Exceptions\AppleSignInException
     */
    protected function getUserByToken($token): array
    {
        $tokenData = $this->decode($token);

        if (!in_array($tokenData['aud'], $this->clientId, true)) {
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
                'id'               => data_get($user, 'sub'),
                'email'            => data_get($user, 'email'),
                'is_private_email' => data_get($user, 'is_private_email', false),
            ]);
    }

    /**
     * @param string $token
     * @return array
     * @throws \Nerdzlab\SocialiteAppleSignIn\Exceptions\AppleSignInException
     */
    private function decode(string $token): array
    {
        try {
            $payload = JWT::decode(
                $token,
                new Key($this->getPublicKey($this->extractKid($token)), self::ALGORITHM),
            );
        } catch (Throwable $exception) {
            throw AppleSignInException::invalidToken($exception);
        }

        return (array)$payload;
    }

    /**
     * @param string $token
     * @return string
     * @throws \Nerdzlab\SocialiteAppleSignIn\Exceptions\AppleSignInException
     */
    private function extractKid(string $token): string
    {
        try {
            $header = JWT::jsonDecode(JWT::urlsafeB64Decode(Str::before($token, '.')));
        } catch (Throwable $exception) {
            throw AppleSignInException::invalidToken($exception);
        }

        if (!$header || !isset($header->kid) || !$kid = (string)$header->kid) {
            throw AppleSignInException::invalidKid();
        }

        return $kid;
    }

    /**
     * @param string $keyId
     * @return string
     * @throws \Nerdzlab\SocialiteAppleSignIn\Exceptions\AppleSignInException
     */
    public function getJWK(string $keyId): string
    {
        $body = $this->getHttpClient()->get(self::JWK_URL)->getBody();

        $parsed = JWK::parseKeySet(json_decode($body, true));

        if (!$key = data_get($parsed, $keyId)) {
            throw AppleSignInException::invalidKid();
        }

        return openssl_pkey_get_details($key->getKeyMaterial())['key'];
    }

    /**
     * @param string $keyId
     * @return string
     */
    private function getPublicKey(string $keyId): string
    {
        return $this->keyStorage()->remember(
            $this->keyPath($keyId),
            config('apple_sign_in.cache.ttl'),
            function () use ($keyId) {
                return $this->getJWK($keyId);
            }
        );
    }

    private function keyPath(string $name): string
    {
        return config('apple_sign_in.cache.prefix') . $name;
    }

    private function keyStorage(): Repository
    {
        return Cache::store(config('apple_sign_in.cache.store'));
    }
}
