<?php

namespace Novius\LaravelNovaTranslatable\Http\Resources;

use Laravel\Nova\Http\Requests\ResourceCreateOrAttachRequest;
use Laravel\Nova\Http\Resources\CreateViewResource;
use Novius\LaravelTranslatable\Traits\Translatable;

class TranslateViewResource extends CreateViewResource
{
    /**
     * From Resource ID.
     *
     * @var string|int|null
     */
    protected $fromResourceId;

    protected string $locale;

    /**
     * Construct a new Create View Resource.
     *
     * @param  string|int|null  $fromResourceId
     * @return void
     */
    public function __construct(string $locale, $fromResourceId = null)
    {
        $this->locale = $locale;
        $this->fromResourceId = $fromResourceId;
    }

    public function newResourceWith(ResourceCreateOrAttachRequest $request)
    {
        $query = $request->findModelQuery($this->fromResourceId);

        $resource = $request->resource();
        $resource::replicateQuery($request, $query);

        $from = $query->firstOrFail();
        $resource = $request->newResourceWith($from);

        $resource->authorizeToReplicate($request);

        $resource = $resource->replicate();

        /** @var Translatable&Model $model */
        $model = $resource->model();
        $model->{$model->getLocaleColumn()} = $this->locale;
        $model->{$model->getLocaleParentIdColumn()} = $from->{$from->getKeyName()};

        if (method_exists($resource, 'translate')) {
            $resource->translate();
        }

        return $resource;
    }
}
