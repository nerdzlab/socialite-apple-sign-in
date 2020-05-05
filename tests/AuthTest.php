<?php

namespace Nerdzlab\LaravelSocialiteAppleSignIn\Tests;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\SocialiteServiceProvider;
use Nerdzlab\LaravelSocialiteAppleSignIn\Exceptions\AppleSignInException;
use Orchestra\Testbench\TestCase;
use Nerdzlab\LaravelSocialiteAppleSignIn\LaravelSocialiteAppleSignInServiceProvider;

class AuthTest extends TestCase
{
    private $token;

    /** @var \Nerdzlab\LaravelSocialiteAppleSignIn\SignInWithAppleProvider $provider */
    private $provider;

    protected function getPackageProviders($app): array
    {
        return [LaravelSocialiteAppleSignInServiceProvider::class, SocialiteServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->token = DataStub::getToken();

        config(['services.apple.client_id' => DataStub::getClientId()]);

        $mock = new MockHandler([
            new Response(200, [], DataStub::getJWKResponseBody())
        ]);
        $handlerStack = HandlerStack::create($mock);

        $this->provider = Socialite::driver('apple');
        $this->provider->setHttpClient(new Client(['handler' => $handlerStack]));
    }

    public function testExpiredToken(): void
    {
        $this->expectException(AppleSignInException::class);
        $this->expectExceptionMessage('Expired token');

        $this->provider->userFromToken($this->token);
    }

    public function testInvalidHeader(): void
    {
        $this->expectException(AppleSignInException::class);
        $this->expectExceptionMessage('Malformed UTF-8 characters');

        $this->provider->userFromToken(
            '123.' . Str::after($this->token, '.')
        );
    }

    public function testInvalidKid(): void
    {
        $this->expectException(AppleSignInException::class);
        $this->expectExceptionMessage('Invalid public key id.');

        $this->provider->userFromToken(
            'eyJraWQiOiIxMjM0NSIsImFsZyI6IlJTMjU2In0=.' . Str::after($this->token, '.')
        );
    }

    public function testFakeHeader(): void
    {
        $this->expectException(AppleSignInException::class);
        $this->expectExceptionMessage('Invalid public key id.');

        $this->provider->userFromToken(
            'eyJxd2VydHkiOjF9.' . Str::after($this->token, '.')
        );
    }

    public function testSuccessful(): void
    {
        JWT::$timestamp = 1588258184;

        $this->assertEmpty(Cache::get(config('apple-sign-in.cache.prefix') . DataStub::getKid()));

        $user = $this->provider->userFromToken($this->token);

        $this->assertSame($user->getId(), DataStub::geUserId());

        $this->assertNotEmpty(Cache::get(config('apple-sign-in.cache.prefix') . DataStub::getKid()));
    }
}
