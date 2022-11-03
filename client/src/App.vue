<template>
  <VApp>
    <VMain>
      <PageHeader @openDrawer="() => drawer = true"></PageHeader>
      <RouterView class="mx-16 my-8" />
    </VMain>
    <PageFooter></PageFooter>
    <NavDrawer v-model="drawer"></NavDrawer>
    <VSnackbar v-model="snackbar.shown" :timeout="2000">
      {{ snackbar.message }}
      <template v-slot:action="{ attrs }">
        <VBtn text v-bind="attrs" @click="snackbar.shown = false">
          Close
        </VBtn>
      </template>
    </VSnackbar>
  </VApp>
</template>

<script>
import { VApp, VBtn, VMain, VSnackbar } from 'vuetify/lib';
import { RouterView } from 'vue-router';
import PageHeader from './components/PageHeader.vue';
import NavDrawer from './components/NavDrawer.vue';
import PageFooter from './components/PageFooter.vue';
import { mapMutations, mapState } from 'vuex';

export default {
  name: 'App',
  data: () => ({
    drawer: false,
    snackbar: {
      shown: false,
      message: '',
    },
  }),
  computed: {
    ...mapState({ stateSnackbar: 'snackbar' }),
  },
  watch: {
    stateSnackbar(newVal) {
      this.snackbar = newVal;
    },
  },
  methods: {
    ...mapMutations(['openSnackbar']),
  },
  components: {
    VApp,
    VMain,
    RouterView,
    PageHeader,
    NavDrawer,
    PageFooter,
    VSnackbar,
    VBtn
  },
};
</script>
