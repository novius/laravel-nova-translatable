<template>
    <Card class="flex flex-col flex-wrap">
        <div class="flex p-4" v-if="locales.length > 2">
            <div class="font-bold text-90 mr-2">
                {{ card.localeLabel }}
            </div>
            <ul

                    class="flex list-reset locale-list"
            >
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

export default {

    components: {
        Locale
    },

    props: [
        'card',
    ],

    data: () => ({
        locales: [],
    }),

    mounted() {
      this.locales = this.card.locales;
    },

    methods: {
        selectedLocale(event) {
            const { key } = event;

            let freshLocales = [];
            this.locales.forEach(locale => {
                locale['isCurrent'] = locale.key === key;

                freshLocales.push(locale);
            });

            this.locales = freshLocales;

            this.saveCurrentLocale(key);
        },

      saveCurrentLocale(key) {
            Nova.request({
                url: '/nova-vendor/laravel-nova-translatable/update-current-locale',
                method: 'POST',
                params: {
                    resource: this.card.resource,
                    url: window.location.href,
                    locale: key
                },
            }).then(({ data }) => {
                if (data.error) {
                    Nova.error(data.message);
                } else {
                    window.location.href = data.redirectUrl;
                }
            });
        }

    },
}
</script>
