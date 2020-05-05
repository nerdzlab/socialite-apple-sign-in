<?php

namespace Nerdzlab\SocialiteAppleSignIn\Tests;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Cache\Repository as CacheInterface;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\SocialiteServiceProvider;
use Mockery;
use Nerdzlab\SocialiteAppleSignIn\Exceptions\AppleSignInException;
use Nerdzlab\SocialiteAppleSignIn\SocialiteAppleSignInServiceProvider;
use Orchestra\Testbench\TestCase;

class AuthTest extends TestCase
{
    private $token;

    protected function getPackageProviders($app): array
    {
        return [
            SocialiteServiceProvider::class,
            SocialiteAppleSignInServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->token = DataStub::token();

        config(['services.apple.client_id' => DataStub::clientId()]);

        $handler = HandlerStack::create(
            new MockHandler([
                new Response(200, [], DataStub::JWKResponseBody())
            ])
        );

        Socialite::driver('apple')->setHttpClient(new Client(['handler' => $handler]));
    }

    public function testExpiredToken(): void
    {
        $this->expectException(AppleSignInException::class);
        $this->expectExceptionMessage('Expired token');

        Socialite::driver('apple')->userFromToken($this->token);
    }

    public function testInvalidHeader(): void
    {
        $this->expectException(AppleSignInException::class);
        $this->expectExceptionMessage('Malformed UTF-8 characters');

        Socialite::driver('apple')->userFromToken(DataStub::tokenWithInvalidHeader());
    }

    public function testInvalidKid(): void
    {
        $this->expectException(AppleSignInException::class);
        $this->expectExceptionMessage('Invalid public key id.');

        Socialite::driver('apple')->userFromToken(DataStub::tokenWithInvalidKid());
    }

    public function testFakeHeader(): void
    {
        $this->expectException(AppleSignInException::class);
        $this->expectExceptionMessage('Invalid public key id.');

        Socialite::driver('apple')->userFromToken(DataStub::tokenWithFakeHeader());
    }

    public function testSuccessful(): void
    {
        $this->mockCache();

        JWT::$timestamp = DataStub::tokenValidTime();

        $user = Socialite::driver('apple')->userFromToken($this->token);

        $this->assertSame($user->getId(), DataStub::userId());
    }

    /** @noinspection PhpParamsInspection */
    private function mockCache(): void
    {
        $store = Mockery::mock(CacheInterface::class)
                        ->shouldReceive('remember')
                        ->once()
                        ->withArgs(static function ($key, $ttl) {
                            return $key === config('apple_sign_in.cache.prefix') . DataStub::kid()
                                && $ttl === config('apple_sign_in.cache.ttl');
                        })
                        ->andReturn(DataStub::tokenBody())
                        ->getMock();


        Cache::shouldReceive('store')
             ->once()
             ->andReturn($store);
    }
}
