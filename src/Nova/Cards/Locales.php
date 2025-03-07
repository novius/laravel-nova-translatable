<?php

namespace Novius\LaravelNovaTranslatable\Nova\Cards;

use Illuminate\Support\Arr;
use Laravel\Nova\Card;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelNovaTranslatable\Helpers\SessionHelper;
use Novius\LaravelTranslatable\Traits\Translatable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

/**
 * @method static static make()
 */
class Locales extends Card
{
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = '1/3';

    public $height = 'dynamic';

    protected array $locales = [];

    private string $resource;

    public $component = 'laravel-nova-locale-selector';

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct()
    {
        parent::__construct();

        $request = app()->get(NovaRequest::class);
        $resource = $request->newResource();
        $model = $resource->model();
        if (! in_array(Translatable::class, class_uses_recursive($model), true)) {
            throw new RuntimeException('Resource must use trait Novius\LaravelTranslatable\Traits\Translatable');
        }

        if (method_exists($resource, 'availableLocales')) {
            $this->locales = $resource->availableLocales();
        }
        $this->resource = $resource::uriKey();
    }

    public function locales(array $locales): static
    {
        $this->locales = $locales;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $currentLocale = SessionHelper::currentLocale($this->resource);

        $locales = array_values(Arr::map($this->locales, static function ($label, $key) use ($currentLocale) {
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
