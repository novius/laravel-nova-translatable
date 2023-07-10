<?php

namespace Novius\LaravelNovaTranslatable\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Novius\LaravelNovaTranslatable\Helpers\SessionHelper;
use Novius\LaravelTranslatable\Traits\Translatable;
use RuntimeException;

/**
 * @method static static make(array $locales, Resource $resource)
 */
class LocaleFilter extends Filter
{
    public $component = 'select-filter';

    protected array $locales = [];

    private Resource $resource;

    public function __construct()
    {
        /** @var NovaRequest $request */
        $request = app()->get(NovaRequest::class);
        /** @var \Laravel\Nova\Resource $resource */
        $resource = $request->newResource();
        $model = $resource->model();
        if (! in_array(Translatable::class, class_uses_recursive($model))) {
            throw new RuntimeException('Resource must use trait Novius\LaravelTranslatable\Traits\Translatable');
        }

        if (method_exists($resource, 'availableLocales')) {
            $this->locales = $resource->availableLocales();
        }
        $this->resource = $resource;

        $this->name = trans('laravel-nova-translatable::messages.language');
    }

    public function locales(array $locales): static
    {
        $this->locales = $locales;

        return $this;
    }

    public function apply(Request $request, $query, $value)
    {
        SessionHelper::setCurrentLocale($value);

        return $query->where(with(new $this->resource->resource)->getLocaleColumn(), $value);
    }

    public function options(Request $request)
    {
        return collect($this->locales)->flip()->toArray();
    }

    public function default()
    {
        return SessionHelper::currentLocale($this->resource->uriKey());
    }
}
