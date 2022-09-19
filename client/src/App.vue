<template>
  <VApp>
    <PageHeader></PageHeader>
    <VMain>
      <RouterView />
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
