<?php

namespace Nerdzlab\LaravelSocialiteAppleSignIn\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\SocialiteServiceProvider;
use Orchestra\Testbench\TestCase;
use Nerdzlab\LaravelSocialiteAppleSignIn\LaravelSocialiteAppleSignInServiceProvider;

class CommandTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [LaravelSocialiteAppleSignInServiceProvider::class, SocialiteServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.apple.client_id' => DataStub::getClientId()]);

        $mock = new MockHandler([
            new Response(200, [], DataStub::getJWKResponseBody())
        ]);
        $handlerStack = HandlerStack::create($mock);

        $this->app->instance(Client::class, new Client(['handler' => $handlerStack]));
    }

    public function testExecution(): void
    {
        $this->assertEmpty(Cache::get(config('apple-sign-in.cache.prefix') . DataStub::getKid()));

        $this->artisan('apple-sign-in:update-jwks')
             ->expectsOutput('All keys successfully updated.');

        $this->assertNotEmpty(Cache::get(config('apple-sign-in.cache.prefix') . DataStub::getKid()));
    }
}
