<?php

namespace Novius\LaravelNovaTranslatable\Nova\Fields;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelTranslatable\Traits\Translatable;
use RuntimeException;

/**
 * @method static static make(mixed $name = null, string|\Closure|callable|object|null $attribute = null, callable|null $resolveCallback = null)
 */
class Translations extends Text
{
    public array $locales = [];

    public function __construct($name = null, $attribute = null, callable $resolveCallback = null)
    {
        $name = $name ?? trans('laravel-nova-translatable::messages.translations');

        parent::__construct($name, function (Model $model) {
            /** @var NovaRequest $request */
            $request = app()->get(NovaRequest::class);
            $resource = $request->newResource();
            $locales = $this->locales ?? (method_exists($resource, 'availableLocales') ? $resource->availableLocales() : null);
            if (! in_array(Translatable::class, class_uses_recursive($model))) {
                throw new RuntimeException('Resource must use trait Novius\LaravelTranslatable\Traits\Translatable');
            }

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

        $this->asHtml()
            ->canSee(function () {
                return count($this->locales) > 1;
            });
    }

    public function locales(array $locales): static
    {
        $this->locales = $locales;

        return $this;
    }
}
