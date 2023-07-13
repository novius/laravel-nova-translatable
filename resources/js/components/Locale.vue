<template>
    <li class="mr-2 flex">
        <button
            class="dim align-middle"
            v-bind:class="{ 'font-bold': locale.isCurrent }"
            v-on:click="setCurrentLocale"
        >
          <span v-if="!locale.key" v-text="locale.label" />
          <img v-if="locale.key" width="24"
               :src="'/vendor/laravel-nova-translatable/images/flags/' + locale.key + '.svg'"
               :title="locale.label"
               :alt="locale.label"
               v-bind:class="{ 'opacity-50': !locale.isCurrent, 'shadow-lg': locale.isCurrent }"
          />
        </button>
    </li>
</template>

<script>
  export default {
    props: [
      'locale',
    ],

    methods: {
      setCurrentLocale() {
        if (!this.locale.isCurrent) {
          this.$emit('selected-locale', {key : this.locale.key});
        }
      }
    },
  }
</script>

<style scoped>


</style>
