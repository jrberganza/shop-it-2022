<template>
  <VApp>
    <VMain>
      <PageHeader @openDrawer="() => drawer = true"></PageHeader>
      <RouterView class="mx-16 my-8" />
    </VMain>
    <PageFooter></PageFooter>
    <NavDrawer v-model="drawer"></NavDrawer>
  </VApp>
</template>

<script>
import { VApp, VMain } from 'vuetify/lib';
import { RouterView } from 'vue-router';
import PageHeader from './components/PageHeader.vue';
import NavDrawer from './components/NavDrawer.vue';
import PageFooter from './components/PageFooter.vue';

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
    NavDrawer,
    PageFooter
  },
};
</script>
