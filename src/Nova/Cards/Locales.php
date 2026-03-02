<?php

namespace Novius\LaravelNovaTranslatable\Nova\Cards;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Laravel\Nova\Card;
use Laravel\Nova\Http\Requests\NovaRequest;
use LaravelLang\Locales\Data\LocaleData;
use LaravelLang\Locales\Facades\Locales as LaravelLangLocales;
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
        /** @var Translatable&Model $model */
        $model = $resource->model() ?? $resource::newModel();
        if (! in_array(Translatable::class, class_uses_recursive($model), true)) {
            throw new RuntimeException('Resource must use trait Novius\LaravelTranslatable\Traits\Translatable');
        }

        $this->locales = LaravelLangLocales::installed()
            ->filter(fn (LocaleData $locale) => $model === null || in_array($locale->code, $model->translatableConfig()->available_locales, true))
            ->mapWithKeys(fn (LocaleData $locale) => [$locale->code => $locale->localized]);
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
