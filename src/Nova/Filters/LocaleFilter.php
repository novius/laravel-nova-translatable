<?php

namespace Novius\LaravelNovaTranslatable\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Resource;
use Novius\LaravelNovaTranslatable\Helpers\SessionHelper;
use Novius\LaravelTranslatable\Traits\Translatable;

/**
 * @method static static make(array $locales, Resource $resource)
 */
class LocaleFilter extends Filter
{
    protected array $locales = [];

    private Resource $resource;

    public function __construct(array $locales, Resource $resource)
    {
        if (! in_array(Translatable::class, class_uses_recursive($resource->resource))) {
            throw new \RuntimeException('Resource must use trait Novius\LaravelTranslatable\Traits\Translatable');
        }
        $this->locales = $locales;
        $this->resource = $resource;

        $this->name = trans('laravel-nova-translatable::messages.language');
    }

    public $component = 'select-filter';

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
