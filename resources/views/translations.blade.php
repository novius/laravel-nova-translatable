<div class="whitespace-nowrap">
@foreach($translations as $translation)
    @php
        $locale = $translation->{$translation->getLocaleColumn()};
        $trans = \Illuminate\Support\Arr::get($locales, $locale, $locale);
    @endphp
    <a href="{{ str_replace('{id}', $translation->{$translation->getKeyName()}, $link) }}">
        <img src="{{ asset('vendor/laravel-nova-translatable/images/flags/'.$locale.'.png') }}"
             title="{{ $trans }}"
             alt="{{ $trans }}"
             class="inline-block"
        />
    </a>
@endforeach
</div>
