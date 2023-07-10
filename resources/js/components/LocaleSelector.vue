<template>
  <Card class="flex flex-col flex-wrap">
    <div class="flex p-4" v-if="locales.length > 2">
      <div class="font-bold text-90 mr-2">
        {{ card.localeLabel }}
      </div>
      <ul class="flex list-reset locale-list">
        <locale
          v-for="(locale) in locales"
          v-bind:locale="locale"
          @selected-locale="selectedLocale($event)"
        ></locale>
      </ul>
    </div>
  </Card>
</template>

<script>
import Locale from './Locale'
import qs from 'qs'

export default {

  components: {
    Locale
  },

  props: [
    'card',
  ],

  data: () => ({
    locales: [],
    oldSearch: '',
  }),

  mounted() {
    this.oldSearch = document.location.search;
    const bodyList = document.querySelector("body")
    const that = this;

    const observer = new MutationObserver(function (mutations) {
      if (that.oldSearch !== document.location.search) {
        const querystring = qs.parse(document.location.search.substring(1));
        if (querystring[that.card.resource + '_filter']) {
          const filters = JSON.parse(atob(querystring[that.card.resource + '_filter']));
          let locale = '';
          filters.forEach(filter => {
            if (filter['Novius\\LaravelNovaTranslatable\\Nova\\Filters\\LocaleFilter']) {
              locale = filter['Novius\\LaravelNovaTranslatable\\Nova\\Filters\\LocaleFilter'];
            }
          });
          that.selectedLocale({
            key: locale,
            notReload: true,
          });
        }
        that.oldSearch = document.location.search;
      }
    });

    observer.observe(bodyList, {
      childList: true,
      subtree: true
    });

    this.locales = this.card.locales;
  },

  methods: {
    selectedLocale(event) {
      const {key} = event;

      let freshLocales = [];
      this.locales.forEach(locale => {
        locale['isCurrent'] = locale.key === key;

        freshLocales.push(locale);
      });

      this.locales = freshLocales;

      this.saveCurrentLocale(key, !event.notReload);
    },

    saveCurrentLocale(key, reload) {
      Nova.request({
        url: '/nova-vendor/laravel-nova-translatable/update-current-locale',
        method: 'POST',
        params: {
          resource: this.card.resource,
          url: window.location.href,
          locale: key,
        },
      }).then(({data}) => {
        if (data.error) {
          Nova.error(data.message);
        } else if (reload) {
          window.location.href = data.redirectUrl;
        }
      });
    }
  },
}
</script>
