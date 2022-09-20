<template>
  <VApp>
    <VMain>
      <PageHeader></PageHeader>
      <RouterView class="mx-16 mt-8" />
    </VMain>
  </VApp>
</template>

<script>
import { VApp, VMain } from 'vuetify/lib';
import { RouterView } from 'vue-router';
import PageHeader from './components/PageHeader.vue';

export default {
  name: 'App',
  data: () => ({}),
  components: {
    VApp, VMain, RouterView, PageHeader,
  },
};
</script>
