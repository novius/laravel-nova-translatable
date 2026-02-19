<?php

namespace Novius\LaravelNovaTranslatable\Nova\Filters;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Novius\LaravelNovaTranslatable\Helpers\SessionHelper;
use Novius\LaravelTranslatable\Traits\Translatable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

/**
 * @method static static make()
 */
class LocaleFilter extends Filter
{
    public $component = 'select-filter';

    protected array $locales = [];

    private Resource $resource;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct()
    {
        /** @var NovaRequest $request */
        $request = app()->get(NovaRequest::class);
        /** @var \Laravel\Nova\Resource $resource */
        $resource = $request->newResource();
        /** @var Translatable&Model $model */
        $model = $resource->model() ?? $resource::newModel();
        if (! in_array(Translatable::class, class_uses_recursive($model), true)) {
            throw new RuntimeException('Resource must use trait Novius\LaravelTranslatable\Traits\Translatable');
        }

        $this->locales($model->translatableConfig()->available_locales);
        $this->resource = $resource;

        $this->name = trans('laravel-nova-translatable::messages.language');
    }

    public function locales(array $locales): static
    {
        $this->locales = $locales;

        return $this;
    }

    public function apply(Request $request, $query, $value): Builder
    {
        SessionHelper::setCurrentLocale($value);
        /** @var Translatable&Model $model */
        $model = $this->resource::newModel();

        return $query->where($model->translatableConfig()->locale_column, $value);
    }

    public function options(Request $request): array
    {
        return collect($this->locales)->flip()->toArray();
    }

    public function default()
    {
        return SessionHelper::currentLocale($this->resource::uriKey());
    }
}
