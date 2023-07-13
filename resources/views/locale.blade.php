@php
    $trans = \Illuminate\Support\Arr::get($locales, $locale, $locale);
@endphp
<img src="{{ asset('vendor/laravel-nova-translatable/images/flags/'.$locale.'.svg') }}"
     title="{{ $trans }}"
     alt="{{ $trans }}"
     class="inline-block"
     width="24"
/>
