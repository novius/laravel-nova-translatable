<?php

namespace Novius\LaravelNovaTranslatable\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelNovaTranslatable\Helpers\SessionHelper;
use Novius\LaravelNovaTranslatable\Nova\Filters\LocaleFilter;

class LocaleController extends Controller
{
    public function updateCurrentLocale(NovaRequest $request)
    {
        try {
            $resourceKey = $request->get('resource');
            $locale = $request->get('locale');
            $url = $request->get('url');

            if ($locale) {
                SessionHelper::setCurrentLocale($locale, $resourceKey);
            } else {
                SessionHelper::clearCurrentLocale($resourceKey);
            }

            $url_parsed = parse_url($url);
            parse_str($url_parsed['query'] ?? '', $query);
            $filter_string = Arr::get($query, $resourceKey.'_filter');
            if (! empty($filter_string)) {
                $filters = json_decode(base64_decode($filter_string), true);
                $found = false;
                foreach ($filters as $i => $filter) {
                    if (array_key_exists(LocaleFilter::class, $filter)) {
                        if ($locale) {
                            $filters[$i][LocaleFilter::class] = $locale;
                        } else {
                            unset($filters[$i]);
                        }
                        $found = true;
                    }
                }
                if (! $found && $locale) {
                    $filters[] = [LocaleFilter::class => $locale];
                }
            } elseif ($locale) {
                $filters = [[LocaleFilter::class => $locale]];
            } else {
                $filters = null;
            }
            if (! empty($filters)) {
                $query[$resourceKey.'_filter'] = base64_encode(json_encode($filters));
            } else {
                unset($query[$resourceKey.'_filter']);
            }

            return response()->json([
                'redirectUrl' => $url_parsed['scheme'].'://'.$url_parsed['host'].$url_parsed['path'].'?'.http_build_query($query),
                'error' => 0,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => 1,
            ]);
        }
    }
}
