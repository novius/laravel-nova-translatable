<div class="whitespace-nowrap">
@foreach($translations as $locale => $translation)
    @php
        $trans = \Illuminate\Support\Arr::get($locales, $locale, $locale);
        $deleted = false;
        if ($translation) {
            $route = route('nova.pages.edit', ['resource' => $resource, 'resourceId' => $translation->{$translation->getKeyName()}]);
            if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($model)) && $translation->{$model->getDeletedAtColumn()}) {
                $route = route('nova.pages.detail', ['resource' => $resource, 'resourceId' => $translation->{$translation->getKeyName()}]);
                $deleted = true;
            }
        } else {
            $route = route('nova.pages.translate', ['resource' => $resource, 'resourceId' => $model->{$model->getKeyName()}, 'locale' => $locale]);
        }
    @endphp
    <a href="{{ $route }}">
        <span style="width: {{ $flagWidth }}px; height: {{ $flagWidth * 3/4 }}px"
             @class([
                'inline-block',
                'relative',
                'pr-5'
             ])
        >
            <img src="{{ asset('vendor/laravel-nova-translatable/images/flags/'.$locale.'.svg') }}"
                 title="{{ $trans }}"
                 alt="{{ $trans }}"
                 @class([
                    'inline-block',
                    'absolute',
                    'opacity-50' => $translation === null || $deleted,
                 ])
                 width="{{ $flagWidth }}"
            />
            @if($deleted)
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                     @class([
                        'inline-block',
                        'absolute',
                     ])
                     width="10" style="left:-5px; top:-5px;">
                  <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                </svg>
            @endif
        </span>
    </a>
@endforeach
</div>
