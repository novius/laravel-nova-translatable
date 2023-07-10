<?php

namespace Novius\LaravelNovaTranslatable;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Novius\LaravelNovaTranslatable\Http\Controllers\LocaleController;

class LaravelNovaTranslatableServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $this->routes();
        });

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'laravel-nova-translatable');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-nova-translatable');

        $this->publishes([
            __DIR__.'/../lang' => lang_path('vendor/laravel-nova-translatable'),
        ], 'lang');

        $this->publishes([
            __DIR__.'/../resources' => public_path('vendor/laravel-nova-translatable'),
        ], 'public');

        Nova::serving(function (ServingNova $event) {
            Nova::script('laravel-nova-translatable', __DIR__.'/../dist/js/card.js');
        });
    }

    protected function routes(): void
    {
        Route::middleware(['nova:api'])
            ->prefix('nova-vendor/laravel-nova-translatable')
            ->post('/update-current-locale', [LocaleController::class, 'updateCurrentLocale']);
    }
}
