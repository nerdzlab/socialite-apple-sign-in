<?php

namespace Nerdzlab\SocialiteAppleSignIn;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Nerdzlab\SocialiteAppleSignIn\Console\UpdateJWKs;

class SocialiteAppleSignInServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $socialite = $this->app->make(Socialite::class);

        $socialite->extend('apple', static function ($app) use ($socialite) {
            $config = array_merge([
                'client_secret' => null,
                'redirect'      => null
            ], $app['config']['services.apple']);

            $config['client_id'] = Arr::wrap($app['config']['services.apple.client_id']);

            return $socialite->buildProvider(SignInWithAppleProvider::class, $config);
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/apple_sign_in.php' => config_path('apple_sign_in.php'),
            ], 'config');

            $this->commands([
                UpdateJWKs::class
            ]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/apple_sign_in.php', 'apple_sign_in');
    }
}
