<?php

namespace Novius\LaravelNovaTranslatable\Nova\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Exceptions\HelperNotSupported;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\ResourceCreateOrAttachRequest;
use LaravelLang\Locales\Data\LocaleData;
use LaravelLang\Locales\Facades\Locales;
use Novius\LaravelTranslatable\Traits\Translatable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @method static static make(mixed $name = null, string|Closure|callable|object|null $attribute = null, callable|null $resolveCallback = null)
 */
class Translations extends Text
{
    public array $locales = [];

    public int $flagWidth = 18;

    public bool $onlyMissing = false;

    public bool $withoutMissing = false;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws HelperNotSupported
     */
    public function __construct($name = null, $attribute = null, ?callable $resolveCallback = null)
    {
        $name = $name ?? trans('laravel-nova-translatable::messages.translations');

        /** @var NovaRequest $request */
        $request = app()->get(NovaRequest::class);
        $resource = $request->newResource();
        /** @var Translatable&Model $model */
        $model = $resource->model() ?? $resource::newModel();

        $is_translatable = in_array(Translatable::class, class_uses_recursive($model), true);
        if ($is_translatable) {
            $attribute = $attribute ?? $model->translatableConfig()->locale_parent_id_column;

            $this->locales(
                Locales::installed()
                    ->filter(fn (LocaleData $locale) => in_array($locale->code, $model->translatableConfig()->available_locales, true))
                    ->mapWithKeys(fn (LocaleData $locale) => [$locale->code => $locale->localized])
                    ->toArray()
            );
        }

        parent::__construct($name, $attribute, $resolveCallback);

        if ($is_translatable) {
            $this->asHtml()
                ->displayUsing(function ($value, $model) use ($resource) {
                    /** @var Translatable&Model $model */
                    if (! empty($this->locales)) {
                        $translations = [];
                        foreach ($this->locales as $locale => $trad) {
                            $translation = $locale === $model->{$model->translatableConfig()->locale_column} ?
                                $model :
                                $model->translationsWithDeleted->firstWhere($model->translatableConfig()->locale_column, $locale);

                            if (($this->withoutMissing && $translation) || ($this->onlyMissing && $translation === null) ||
                                (! $this->withoutMissing && ! $this->onlyMissing)
                            ) {
                                $translations[$locale] = $translation;
                            }
                        }
                    } else {
                        $translations = $model->translations->mapWithKeys(function ($item) use ($model) {
                            return [$item->{$model->translatableConfig()->locale_column} => $item];
                        });
                    }

                    return (string) view('laravel-nova-translatable::translations', [
                        'flagWidth' => $this->flagWidth,
                        'locales' => $this->locales,
                        'model' => $model,
                        'resource' => $resource::uriKey(),
                        'translations' => $translations,
                    ]);
                })
                ->canSee(function () {
                    return count($this->locales) > 1;
                });
        }
    }

    public function locales(array $locales): static
    {
        $this->locales = $locales;

        return $this;
    }

    public function flagWidth(int $width): static
    {
        $this->flagWidth = $width;

        return $this;
    }

    public function onlyMissing(): static
    {
        $this->onlyMissing = true;

        return $this;
    }

    public function withoutMissing(): static
    {
        $this->withoutMissing = true;

        return $this;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function jsonSerialize(): array
    {
        $request = app()->get(NovaRequest::class);

        return array_merge(parent::jsonSerialize(), [
            'component' => $request instanceof ResourceCreateOrAttachRequest ? 'hidden-field' : $this->component(),
        ]);
    }
}
