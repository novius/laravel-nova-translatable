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

## Action Translate

You can add the `Translate` action on your Nova Resource:

```php
use Laravel\Nova\Resource;
use Novius\LaravelNovaTranslatable\Nova\Actions\Translate;

class Post extends Resource
{
    public function actions(NovaRequest $request): array
    {
        return [
            Translate::make()
                ->onModel($this->resource::class)
                ->locales(['fr' => 'Français', 'en' => 'English'])
                ->titleField('name')
                ->titleLabel('Name')
                ->redirectAfterTranslate(false)
                ->onlyInline(),
        ];
    }
```

## Field Locale

You can add the `Locale` field on your Nova Resource:

```php
use Laravel\Nova\Resource;
use Novius\LaravelNovaTranslatable\Nova\Fields\Locale;

class Post extends Resource
{
    public function fields(NovaRequest $request): array
    {
        return [
            Locale::make(trans('laravel-nova-news::crud-post.language'), 'locale')
                ->options(['fr' => 'Français', 'en' => 'English']),
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
