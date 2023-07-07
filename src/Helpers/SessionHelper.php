<?php

namespace Novius\LaravelNovaTranslatable\Helpers;

class SessionHelper
{
    protected static function sessionKey(string $resource = null): string
    {
        if ($resource !== null) {
            return 'laravel_nova_translatable_'.$resource.'_current_locale';
        }

        return 'laravel_nova_translatable_current_locale';
    }

    public static function currentLocale(string $resource = null): ?string
    {
        return session()->get(static::sessionKey($resource));
    }

    public static function clearCurrentLocale(string $resource = null): void
    {
        session()->forget(static::sessionKey($resource));
    }

    public static function setCurrentLocale(string $locale, string $resource = null): void
    {
        session()->put(static::sessionKey($resource), $locale);
    }
}
