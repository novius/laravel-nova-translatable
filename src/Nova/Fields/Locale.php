<?php

namespace Novius\LaravelNovaTranslatable\Nova\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Exceptions\HelperNotSupported;
use Laravel\Nova\Fields\AsHTML;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use LaravelLang\Locales\Data\LocaleData;
use LaravelLang\Locales\Facades\Locales;
use Novius\LaravelNovaTranslatable\Helpers\SessionHelper;
use Novius\LaravelTranslatable\Traits\Translatable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @method static static make(mixed $name = null, string|Closure|callable|object|null $attribute = null, callable|null $resolveCallback = null)
 */
class Locale extends Select
{
    use AsHTML;

    public $copyable = false;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws HelperNotSupported
     */
    public function __construct($name = null, $attribute = null, ?callable $resolveCallback = null)
    {
        $name = $name ?? trans('laravel-nova-translatable::messages.language');

        $request = app()->get(NovaRequest::class);
        $resource = $request->newResource();
        /** @var Translatable&Model $model */
        $model = $resource->model() ?? $resource::newModel();

        $is_translatable = in_array(Translatable::class, class_uses_recursive($model), true);
        if ($is_translatable) {
            $attribute = $attribute ?? $model->translatableConfig()->locale_column;
        }

        parent::__construct($name, $attribute, $resolveCallback);

        $this->rules('required')
            ->sortable()
            ->showOnIndex(function () {
                $options = value($this->optionsCallback);

                return count($options) > 1;
            })
            ->default(function () use ($resource) {
                $options = value($this->optionsCallback);
                if (count($options) === 1) {
                    return array_keys($options)[0];
                }

                return SessionHelper::currentLocale($resource::uriKey());
            });

        if ($is_translatable) {
            $locales = Locales::installed()
                ->filter(fn (LocaleData $locale) => $model === null || in_array($locale->code, $model->translatableConfig()->available_locales, true))
                ->mapWithKeys(fn (LocaleData $locale) => [$locale->code => $locale->localized])
                ->toArray();
            $this->options($locales)
                ->displayUsing(function ($value) use ($locales) {
                    return (string) view('laravel-nova-translatable::locale', [
                        'locales' => $locales,
                        'locale' => $value,
                    ]);
                })
                ->asHtml();
        } else {
            $this->displayUsingLabels();
        }
    }

    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'asHtml' => $this->asHtml,
        ]);
    }
}
