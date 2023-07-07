<?php

namespace Novius\LaravelNovaTranslatable\Nova\Fields;

use Laravel\Nova\Fields\Select;
use Laravel\Nova\Resource;
use Novius\LaravelNovaTranslatable\Helpers\SessionHelper;
use Novius\LaravelTranslatable\Traits\Translatable;

class Locale extends Select
{
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->rules('required')
            ->sortable()
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

                return SessionHelper::currentLocale($this->resource?->uriKey()) ?? null;
            });
    }

    public function resource(Resource $resource): static
    {
        if (! in_array(Translatable::class, class_uses_recursive($resource->resource))) {
            throw new \RuntimeException('Resource must use trait Novius\LaravelTranslatable\Traits\Translatable');
        }

        $this->resource = $resource;
        $this->attribute = $resource->resource->getLocaleColumn();

        return $this;
    }
}
