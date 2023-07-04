<?php

namespace Novius\LaravelNovaTranslatable\Nova\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\Resource as NovaResource;
use Novius\LaravelTranslatable\Exceptions\TranslatableException;
use Novius\LaravelTranslatable\Traits\Translatable;

class Translate extends Action
{
    protected array $locales = [];

    protected ?string $onModel = null;

    protected ?string $titleField = null;

    protected ?string $titleLabel = null;

    public function locales(array $locales): static
    {
        $this->locales = $locales;

        return $this;
    }

    public function name()
    {
        return $this->name ?: trans('laravel-nova-translatable::messages.translate');
    }

    public function onModel(string $model): static
    {
        $this->onModel = $model;

        return $this;
    }

    public function titleField(string $titleField): static
    {
        $this->titleField = $titleField;

        return $this;
    }

    public function titleLabel(string $titleLabel): static
    {
        $this->titleLabel = $titleLabel;

        return $this;
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        if ($this->onModel === null || ! class_exists($this->onModel)) {
            throw new \RuntimeException('You must define Model of Translate action.');
        }

        if ($models->count() > 1) {
            return Action::danger(trans('laravel-nova-translatable::messages.action_only_available_for_single'));
        }

        try {
            /** @var Model|Translatable $modelToTranslate */
            $modelToTranslate = $models->first();
            /** @var Model|Translatable $modelTranslate */
            $modelTranslate = $modelToTranslate->translate($fields->locale, [
                $this->titleField => $fields->title,
            ]);

            /** @var NovaResource $resourceClass */
            $resourceClass = Nova::resourceForModel($modelToTranslate::class);

            return Action::visit('/resources/'.$resourceClass::uriKey(), [
                'viaResource' => $resourceClass::uriKey(),
                'viaResourceId' => $modelTranslate->{$modelTranslate->getKeyName()},
            ]);
        } catch (TranslatableException $e) {
            return Action::danger($e->getMessage());
        } catch (\Exception $e) {
            return Action::danger(trans('laravel-nova-translatable::messages.error_during_translation'));
        }
    }

    /**
     * Get the fields available on the action.
     */
    public function fields(NovaRequest $request): array
    {
        $fields = [
            Select::make(trans('laravel-nova-news::crud-post.locale'), 'locale')
                ->options($this->locales)
                ->displayUsingLabels()
                ->rules('required', 'in:'.implode(',', array_keys($this->locales))),
        ];
        if ($this->titleField) {
            $fields[] = Text::make($this->titleLabel ?? trans('laravel-nova-translatable::messages.title'), 'title')
                ->required()
                ->rules('required', 'max:255');
        }

        return $fields;
    }
}
