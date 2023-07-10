<?php

namespace Novius\LaravelNovaTranslatable\Nova\Fields;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Translations extends Text
{
    public array $locales = [];

    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, function (Model $model) {
            /** @var NovaRequest $request */
            $request = app()->get(NovaRequest::class);
            $resource = $request->resource();
            $locales = $this->locales ?? (method_exists($resource, 'availableLocales') ? $resource->availableLocales() : null);

            if (! empty($locales)) {
                $translations = [];
                foreach ($locales as $locale => $trad) {
                    $translation = $model->translations->firstWhere($model->getLocaleColumn(), $locale);
                    if ($translation) {
                        $translations[] = $translation;
                    }
                }
            } else {
                $translations = $model->translations;
            }

            return (string) view('laravel-nova-translatable::translations', [
                'link' => config('nova.path').'/resources/'.$resource::uriKey().'/{id}/edit',
                'locales' => $this->locales,
                'model' => $model,
                'translations' => $translations,
            ]);
        }, $resolveCallback);

        $this->asHtml();
    }

    public function locales(array $locales): static
    {
        $this->locales = $locales;

        return $this;
    }
}
