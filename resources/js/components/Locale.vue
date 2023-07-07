<template>
    <li class="mr-2">
        <button
            class="dim"
            v-bind:class="{ 'font-bold': locale.isCurrent }"
            v-on:click="setCurrentLocale"
        >
            {{ locale.label }}
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
