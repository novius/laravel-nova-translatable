<div class="whitespace-nowrap">
@foreach($translations as $locale => $translation)
    @php
        $trans = \Illuminate\Support\Arr::get($locales, $locale, $locale);
    @endphp
    @if($translation)
        <a href="{{ route('nova.pages.edit', ['resource' => $resource, 'resourceId' => $model->{$model->getKeyName()}]) }}">
            <img src="{{ asset('vendor/laravel-nova-translatable/images/flags/'.$locale.'.svg') }}"
                 title="{{ $trans }}"
                 alt="{{ $trans }}"
                 class="inline-block"
                 width="{{ $flagWidth }}"
            />
        </a>
    @else
        <a href="{{ route('nova.pages.translate', ['resource' => $resource, 'resourceId' => $model->{$model->getKeyName()}, 'locale' => $locale]) }}">
            <img src="{{ asset('vendor/laravel-nova-translatable/images/flags/'.$locale.'.svg') }}"
                 title="{{ $trans }}"
                 alt="{{ $trans }}"
                 class="inline-block opacity-50"
                 width="{{ $flagWidth }}"
            />
        </a>
    @endif
@endforeach
</div>
