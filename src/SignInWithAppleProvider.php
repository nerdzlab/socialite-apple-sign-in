<?php

namespace Nerdzlab\LaravelAppleSignIn;

use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Nerdzlab\LaravelAppleSignIn\Exceptions\AuthFailedException;
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
                'name'  => request()->input('name', ''),
                'email' => $user['email'] && !array_key_exists('is_private_email', $user) ? $user['email'] : null,
            ]);
    }


    private function validateToken(string $token): array
    {
        $tokenData = $this->checkTokenSignature($token);

        if ($this->clientId !== $tokenData['aud']) {
            throw new AuthFailedException('Invalid client_id.');
        }

        if (self::TOKEN_ISSUER !== $tokenData['iss']) {
            throw AuthFailedException::invalidToken();
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
                Storage::disk('local')->get($this->keyPath($keyId)),
                [self::VERIFICATION_ALGORITHM]
            );
        } catch (Throwable $exception) {
            throw AuthFailedException::invalidToken($exception);
        }

        return (array)$payload;
    }

    private function extractKeyId(string $token): string
    {
        $header = json_decode(base64_decode(explode('.', $token)[0]), true);

        if (!isset($header['kid']) || !is_string($header['kid'])) {
            throw AuthFailedException::invalidToken();
        }

        return $header['kid'];
    }

    private function getAuthKeys(): array
    {
        $response = $this->getHttpClient()->get('https://appleid.apple.com/auth/keys');

        return json_decode($response->getBody(), true);
    }

    private function prepareKey(string $keyId): void
    {
        if (!Storage::disk('local')->exists($this->keyPath($keyId))) {
            $this->updatePublicKeys();
        }

        if (!Storage::disk('local')->exists($this->keyPath($keyId))) {
            throw AuthFailedException::invalidToken();
        }
    }

    private function updatePublicKeys(): void
    {
        $publicKeys = JWK::parseKeySet($this->getAuthKeys());

        foreach ($publicKeys as $name => $resource) {
            $keyData = openssl_pkey_get_details($resource);

            Storage::disk('local')->put($this->keyPath($name), $keyData['key']);
        }
    }

    private function keyPath(string $name): string
    {
        return 'appleKeys/' . $name . '.pub';
    }
}
