# [2.0.0]

Under the hood, the package now uses version 2.0 of [Laravel Translatable](https://github.com/novius/laravel-translatable). 
Be sure to follow the update instructions in the [documentation to migrate your application to version 2.0](https://github.com/novius/laravel-translatable/blob/main/CHANGELOG.md). 

This version no longer requires defining an `availableLocales` method on your Resource. 
Locales are now retrieved via the `translatableConfig()->availableLocales` method of the `Translatable` trait. 
If you have not set up the locale configuration in your model, this will use the locales present in your [Laravel via laravel-lang/locales](https://laravel-lang.com/basic-usage.html#managing_locales).
