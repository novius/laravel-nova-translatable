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

## Fields, Action, Filter, Card

* Add `Locale` field on your Nova Resource.
* Add `Translations` field on your Nova Resource. Don't forget to add relation `translations` in the eager loading of your resource. You can translate item from the list of flags displayed.
* You can add the `LocaleFilter` filter on your Nova Resource.
* You can add the `Locales` card on your Nova Resource, if you've added the `LocaleFilter`.

In all cases, add an `availableLocales` on your Resource.

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

    public function fields(NovaRequest $request): array
    {
        return [
            Locale::make(),
            Translations::make(),
        ];
    }

    // Optional, if you want to have a bar to switch locale of the items displayed on the index, more accessible than the filters 
    // work with the filter LocaleFilter
    public function cards(NovaRequest $request): array
    {
        return [
            new Locales(),
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new LocaleFilter(),
        ];
    }

    // Optional, if you want to implement custom translation on your model  
    public function translate(): void
    {
         $model = $this->model();
         $model->attribute_to_translate = 'Translation';
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
