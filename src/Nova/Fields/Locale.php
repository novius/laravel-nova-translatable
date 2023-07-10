<?php

namespace Novius\LaravelNovaTranslatable\Nova\Fields;

use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelNovaTranslatable\Helpers\SessionHelper;
use Novius\LaravelTranslatable\Traits\Translatable;
use RuntimeException;

class Locale extends Select
{
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $request = app()->get(NovaRequest::class);
        $resource = $request->newResource();
        $model = $resource->model();
        if (! in_array(Translatable::class, class_uses_recursive($model))) {
            throw new RuntimeException('Resource must use trait Novius\LaravelTranslatable\Traits\Translatable');
        }

        $this->rules('required')
            ->sortable()
            ->displayUsingLabels()
            ->showOnIndex(function () {
                $options = value($this->optionsCallback);

                return count($options) > 1;
            })
            ->default(function () use ($resource) {
                $options = value($this->optionsCallback);
                if (count($options) === 1) {
                    return array_keys($options)[0];
                }

                return SessionHelper::currentLocale($resource::uriKey()) ?? null;
            });

        if (method_exists($resource, 'availableLocales')) {
            $this->options($resource->availableLocales());
        }

        $this->attribute = $model->getLocaleColumn();
    }
}
