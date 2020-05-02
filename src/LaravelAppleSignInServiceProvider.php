<?php

namespace Nerdzlab\LaravelAppleSignIn;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;

class LaravelAppleSignInServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $socialite = $this->app->make(Factory::class);

        $socialite->extend('apple', static function ($app) use ($socialite) {
            $config = array_merge([
                'client_secret' => null,
                'redirect'      => null
            ], $app['config']['services.apple']);

            return $socialite->buildProvider(SignInWithAppleProvider::class, $config);
        });
    }

    public function register(): void
    {

    }
}
