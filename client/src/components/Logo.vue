<template>
  <VImg class="logo" src="/images/logo-d.png" contain max-height="100%" max-width="150px" v-ripple
    @click="() => $router.push('/')" />
</template>

<style>
.logo {
  cursor: pointer;
}
</style>

<script>
import { VImg } from 'vuetify/lib';

export default {
  name: 'Logo',
  components: {
    VImg
  },
}
</script>
