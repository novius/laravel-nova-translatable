<?php

namespace Novius\LaravelNovaTranslatable\Nova\Fields;

use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class Locale extends Select
{
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->rules('required')
            ->sortable()
            ->filterable()
            ->displayUsingLabels()
            ->showOnIndex(function () {
                $options = value($this->optionsCallback);

                return count($options) > 1;
            })
            ->default(function () {
                $options = value($this->optionsCallback);
                if (count($options) === 1) {
                    return array_keys($options)[0];
                }

                return null;
            });

        $request = app()->get(NovaRequest::class);
        $resource = $request->resource();

        if (method_exists($resource, 'availableLocales')) {
            $this->options($resource->availableLocales());
        }
    }
}
