<?php

namespace Novius\LaravelNovaTranslatable\Http\Controllers;

use Inertia\Inertia;
use Laravel\Nova\Http\Controllers\Pages\ResourceReplicateController;
use Laravel\Nova\Http\Requests\ResourceCreateOrAttachRequest;
use Laravel\Nova\Menu\Breadcrumb;
use Laravel\Nova\Menu\Breadcrumbs;
use Laravel\Nova\Nova;

class ResourceTranslateController extends ResourceReplicateController
{
    public function __invoke(ResourceCreateOrAttachRequest $request)
    {
        abort_unless($request->findModelQuery()->exists(), 404);

        $resourceClass = $request->resource();

        return Inertia::render('Nova.Replicate', [
            'breadcrumbs' => $this->breadcrumbs($request),
            'resourceName' => $resourceClass::uriKey(),
            'resourceId' => 'translate|'.$request->query('locale').'|'.$request->resourceId,
            'viaResource' => $request->query('viaResource') ?? '',
            'viaResourceId' => $request->query('viaResourceId') ?? '',
            'viaRelationship' => $request->query('viaRelationship') ?? '',
        ]);
    }

    /**
     * Get breadcrumb menu for the page.
     *
     * @return \Laravel\Nova\Menu\Breadcrumbs
     */
    protected function breadcrumbs(ResourceCreateOrAttachRequest $request)
    {
        $resourceClass = $request->resource();

        return Breadcrumbs::make([
            Breadcrumb::make(Nova::__('Resources')),
            Breadcrumb::resource($resourceClass),
            Breadcrumb::resource($request->findResourceOrFail()),
            Breadcrumb::make(Nova::__('Translate :resource', ['resource' => $resourceClass::singularLabel()])),
        ]);
    }
}
