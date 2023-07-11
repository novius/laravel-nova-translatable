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

    protected ?string $titleField = null;

    protected ?string $titleLabel = null;

    protected bool $redirectAfterTranslate = true;

    public $showOnIndex = false;

    public $showInline = true;

    public function __construct()
    {
        /** @var NovaRequest $request */
        $request = app()->get(NovaRequest::class);
        $resource = $request->newResource();

        if (method_exists($resource, 'availableLocales')) {
            $this->locales($resource->availableLocales());
        }

        return $this->canSee(function () {
            return count($this->locales) > 1;
        });
    }

    public function locales(array $locales): static
    {
        $this->locales = $locales;

        return $this;
    }

    public function name()
    {
        return $this->name ?: trans('laravel-nova-translatable::messages.translate');
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

    public function redirectAfterTranslate(bool $redirect = true): static
    {
        $this->redirectAfterTranslate = $redirect;

        return $this;
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        if (! in_array(Translatable::class, class_uses_recursive($models->first()))) {
            throw new \RuntimeException('Translate action only work on model using Translatable trait.');
        }

        try {
            /** @var Model|Translatable $modelToTranslate */
            $modelToTranslate = $models->first();
            /** @var Model|Translatable $modelTranslate */
            $modelTranslate = $modelToTranslate->translate($fields->locale, [
                $this->titleField => $fields->title,
            ]);
            if (in_array('Spatie\Sluggable\HasSlug', class_uses_recursive($modelToTranslate))) {
                $modelTranslate->generateSlug();
                $modelTranslate->save();
            }

            /** @var NovaResource $resourceClass */
            $resourceClass = Nova::resourceForModel($modelToTranslate::class);

            if ($this->redirectAfterTranslate) {
                return Action::redirect(Nova::path().'/resources/'.$resourceClass::uriKey().'/'.$modelTranslate->{$modelTranslate->getKeyName()}.'/edit');
            }

            return Action::message(trans('laravel-nova-translatable::messages.successfully_translated'));
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
            Select::make(trans('laravel-nova-translatable::messages.language'), 'locale')
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
