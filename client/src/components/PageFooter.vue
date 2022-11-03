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
      if (this.session == null || this.session.role == 'visitor') {
        this.items = [
          {
            title: "Homepage",
            icon: "mdi-home",
            path: "/",
          },
          {
            title: "Login",
            icon: "mdi-login",
            path: "/login/",
          },
          {
            title: "Register",
            icon: "mdi-account",
            path: "/register/",
          },
        ]
      } else if (this.session.role == 'user') {
        this.items = [
          {
            title: "Homepage",
            icon: "mdi-home",
            path: "/",
          },
          {
            title: "My Shop",
            icon: "mdi-shopping",
            path: "/my/shop/",
          },
          {
            title: "Logout",
            icon: "mdi-logout",
            action: () => this.logout(),
          },
        ]
      } else if (this.session.role == 'employee') {
        this.items = [
          {
            title: "Homepage",
            icon: "mdi-home",
            path: "/",
          },
          {
            title: "My Shop",
            icon: "mdi-shopping",
            path: "/my/shop/",
          },
          {
            title: "Category Editor",
            icon: "mdi-tag",
            path: "/categories/",
          },
          {
            title: "Moderation",
            icon: "mdi-shield-sword",
            path: "/moderation/dashboard/",
          },
          {
            title: "Logout",
            icon: "mdi-logout",
            action: () => this.logout(),
          },
        ]
      } else if (this.session.role == 'admin') {
        this.items = [
          {
            title: "Homepage",
            icon: "mdi-home",
            path: "/",
          },
          {
            title: "My Shop",
            icon: "mdi-shopping",
            path: "/my/shop/",
          },
          {
            title: "Category Editor",
            icon: "mdi-tag",
            path: "/categories/",
          },
          {
            title: "Moderation",
            icon: "mdi-shield-sword",
            path: "/moderation/dashboard/",
          },
          {
            title: "Manage Users",
            icon: "mdi-badge-account-horizontal",
            path: "/admin/users/",
          },
          {
            title: "Reports",
            icon: "mdi-file-document",
            path: "/reports/dashboard/",
          },
          {
            title: "Homepage Editor",
            icon: "mdi-home",
            path: "/home/edit/",
          },
          {
            title: "Logout",
            icon: "mdi-logout",
            action: () => this.logout(),
          },
        ]
      }
    }
  },
  components: {
    VFooter, VRow, VCol, VBtn
  },
};
</script>
