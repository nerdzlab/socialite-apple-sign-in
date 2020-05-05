<?php

namespace Nerdzlab\LaravelSocialiteAppleSignIn;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Nerdzlab\LaravelSocialiteAppleSignIn\Console\UpdateJWKs;

class LaravelSocialiteAppleSignInServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $socialite = $this->app->make(Socialite::class);

        $socialite->extend('apple', static function ($app) use ($socialite) {
            $config = $app['config']['services.apple'];

            $config = array_merge([
                'client_secret' => null,
                'redirect'      => null
            ], $config);

            return $socialite->buildProvider(SignInWithAppleProvider::class, $config);
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/apple-sign-in.php' => config_path('apple-sign-in.php'),
            ], 'config');

            $this->commands([
                UpdateJWKs::class
            ]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/apple-sign-in.php', 'apple-sign-in');
    }
}
