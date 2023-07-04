<?php

namespace Novius\LaravelNovaTranslatable;

use Illuminate\Support\ServiceProvider;

class LaravelNovaTranslatableServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'laravel-nova-translatable');

        $this->publishes([
            __DIR__.'/../lang' => lang_path('vendor/laravel-nova-translatable'),
        ], 'lang');
    }
}
