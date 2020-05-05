<?php

namespace Nerdzlab\SocialiteAppleSignIn\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\SocialiteServiceProvider;
use Orchestra\Testbench\TestCase;
use Nerdzlab\SocialiteAppleSignIn\SocialiteAppleSignInServiceProvider;

class CommandTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [SocialiteAppleSignInServiceProvider::class, SocialiteServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.apple.client_id' => DataStub::clientId()]);

        $mock = new MockHandler([
            new Response(200, [], DataStub::JWKResponseBody())
        ]);
        $handlerStack = HandlerStack::create($mock);

        $this->app->instance(Client::class, new Client(['handler' => $handlerStack]));
    }

    public function testExecution(): void
    {
        $this->artisan('apple-sign-in:update-keys')
             ->expectsOutput('All keys successfully updated.');
    }
}
