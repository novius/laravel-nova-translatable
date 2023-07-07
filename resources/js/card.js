import LocaleSelector from './components/LocaleSelector';

Nova.booting((app) => {
  app.component('laravel-nova-locale-selector', LocaleSelector);
});
