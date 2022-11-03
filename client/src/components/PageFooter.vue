<template>
  <VFooter color="primary lighten-1" padless>
    <VRow justify="center" no-gutters>
      <VBtn v-for="item in items" :key="item.title" dark text class="my-2"
        @click="item.action ? item.action() : $router.push(item.path)">
        {{ item.title }}
      </VBtn>
      <VCol class="primary lighten-1 py-4 text-center white--text" cols="12">
        {{ new Date().getFullYear() }} — <strong>ShopIt!</strong>
      </VCol>
    </VRow>
  </VFooter>
</template>

<style>
.toolbar {
  position: sticky;
  top: 0;
  z-index: 10000;
}
</style>

<script>
import {
  VFooter, VRow, VCol, VBtn
} from 'vuetify/lib';
import { getNavItems } from '../navItems';
import { mapState, mapMutations } from 'vuex';

export default {
  name: 'PageFooter',
  data: () => ({
    /** @type {any[]} */ items: []
  }),
  computed: {
    ...mapState(['session']),
  },
  methods: {
    logout() {
      fetch('/api/session/logout.php')
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.$store.dispatch('fetchSession');
            this.$router.push('/');
            this.openSnackbar({ shown: true, message: "Logged out" });
          } else {
            this.openSnackbar({ shown: true, message: json._error });
          }
        });
    },
    ...mapMutations(['openSnackbar']),
  },
  watch: {
    drawer(newVal) {
      this.internalDrawer = newVal;
    },
    session() {
      this.items = getNavItems(this, this.session.role);
    }
  },
  components: {
    VFooter, VRow, VCol, VBtn
  },
};
</script>
