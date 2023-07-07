<?php

namespace Novius\LaravelNovaTranslatable\Nova\Cards;

use Illuminate\Support\Arr;
use Laravel\Nova\Card;
use Laravel\Nova\Resource;
use Novius\LaravelNovaTranslatable\Helpers\SessionHelper;

/**
 * @method static static make(array $locales, Resource $resource)
 */
class Locales extends Card
{
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = '1/3';

    protected array $locales = [];

    private string $resource;

    public $component = 'laravel-nova-locale-selector';

    public function __construct(array $locales, Resource $resource)
    {
        parent::__construct();

        $this->locales = $locales;
        $this->resource = $resource->uriKey();
    }

    public function jsonSerialize(): array
    {
        $currentLocale = SessionHelper::currentLocale($this->resource);

        $locales = array_values(Arr::map($this->locales, function ($label, $key) use ($currentLocale) {
            return [
                'key' => $key,
                'label' => $label,
                'isCurrent' => $currentLocale === $key,
            ];
        }));
        array_unshift($locales, [
            'key' => '',
            'label' => trans('laravel-nova-translatable::messages.locale_all'),
            'isCurrent' => empty($currentLocale),
        ]);

        return array_merge(parent::jsonSerialize(), [
            'resource' => $this->resource,
            'locales' => $locales,
            'localeLabel' => trans('laravel-nova-translatable::messages.language'),
        ]);
    }
}
