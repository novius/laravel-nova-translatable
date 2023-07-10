# Laravel Nova Translatable

[![License: AGPL v3](https://img.shields.io/badge/License-AGPL%20v3-blue.svg)](http://www.gnu.org/licenses/agpl-3.0)

## Introduction 

This package allows you to manage Laravel Models which use [Laravel Translatable](https://github.com/novius/laravel-translatable) in [Laravel Nova](https://nova.laravel.com/).  

## Requirements

* Laravel Nova >= 4.0
* Laravel Translatable >= 1.0
* Laravel >= 8.0

## Installation

You can install the package via composer:

```bash
composer require novius/laravel-nova-translatable
```

## Assets

Next we need to publish the package's assets. We do this by running the following command:

```sh
php artisan vendor:publish --provider="Novius\LaravelNovaTranslatable\LaravelNovaTranslatableServiceProvider" --tag="public"
```

## Action Translate

You can add the `Translate` action on your Nova Resource:

```php
use Laravel\Nova\Resource;
use Novius\LaravelNovaTranslatable\Nova\Actions\Translate;

class Post extends Resource
{
    public static $with = ['translations'];

    public function availableLocales(): array
    {
        return ['fr' => 'Français', 'en' => 'English'];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            Translate::make()
                ->titleField('name')
                ->titleLabel('Name')
                ->redirectAfterTranslate(false)
                ->onlyInline(),
        ];
    }
```

## Field Locale and Translations

You can add `Locale` and `Translations` fields on your Nova Resource.
And don't forget to add relation `translations` in the eager loading of your resource.

```php
use Laravel\Nova\Resource;
use Novius\LaravelNovaTranslatable\Nova\Fields\Locale;
use Novius\LaravelNovaTranslatable\Nova\Fields\Translations;

class Post extends Resource
{
    public static $with = ['translations'];

    public function availableLocales(): array
    {
        return ['fr' => 'Français', 'en' => 'English'];
    }

    public function fields(NovaRequest $request): array
    {
        return [
            Locale::make('Language', 'locale'),
            Translations::make('Translations'),
        ];
    }
```

## Lang files

If you want to customize the lang files, you can publish them with:

```bash
php artisan vendor:publish --provider="Novius\LaravelNovaTranslatable\LaravelNovaTranslatableServiceProvider" --tag="lang"
```

## Lint

Lint your code with Laravel Pint using:

```bash
composer run-script lint
```

## Licence

This package is under [GNU Affero General Public License v3](http://www.gnu.org/licenses/agpl-3.0.html) or (at your option) any later version.
