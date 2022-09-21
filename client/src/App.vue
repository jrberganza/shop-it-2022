<template>
  <VApp>
    <VMain>
      <PageHeader @openDrawer="() => drawer = true"></PageHeader>
      <RouterView class="mx-16 mt-8" />
      <NavDrawer v-model="drawer"></NavDrawer>
    </VMain>
  </VApp>
</template>

<script>
import { VApp, VMain } from 'vuetify/lib';
import { RouterView } from 'vue-router';
import PageHeader from './components/PageHeader.vue';
import NavDrawer from './components/NavDrawer.vue';

export default {
  name: 'App',
  data: () => ({
    drawer: false,
  }),
  components: {
    VApp,
    VMain,
    RouterView,
    PageHeader,
    NavDrawer
  },
};
</script>
