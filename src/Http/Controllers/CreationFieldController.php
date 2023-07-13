<?php

namespace Novius\LaravelNovaTranslatable\Http\Controllers;

use Laravel\Nova\Http\Controllers\CreationFieldController as BaseCreationFieldController;
use Laravel\Nova\Http\Requests\ResourceCreateOrAttachRequest;
use Novius\LaravelNovaTranslatable\Http\Resources\TranslateViewResource;

class CreationFieldController extends BaseCreationFieldController
{
    public function __invoke(ResourceCreateOrAttachRequest $request)
    {
        if ($request->has('fromResourceId')) {
            $fromResourceId = explode('|', $request->get('fromResourceId'), 3);
            if (count($fromResourceId) === 3 && $fromResourceId[0] === 'translate') {
                return TranslateViewResource::make($fromResourceId[1], $fromResourceId[2])->toResponse($request);
            }
        }

        return parent::__invoke($request);
    }
}
