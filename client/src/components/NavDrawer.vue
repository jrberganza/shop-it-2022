<template>
  <VNavigationDrawer v-model="internalDrawer" @input="() => $emit('input', internalDrawer)" absolute temporary
    color="primary" dark class="nav-drawer">
    <VListItem>
      <VListItemContent>
        <VListItemTitle>
          <Logo></Logo>
        </VListItemTitle>
      </VListItemContent>
    </VListItem>

    <VDivider></VDivider>

    <VList dense>
      <VListItem v-for="item in items" :key="item.title" link
        @click="item.action ? item.action() : $router.push(item.path)">
        <VListItemIcon>
          <VIcon>{{ item.icon }}</VIcon>
        </VListItemIcon>

        <VListItemContent>
          <VListItemTitle>{{ item.title }}</VListItemTitle>
        </VListItemContent>
      </VListItem>
    </VList>
  </VNavigationDrawer>
</template>

<style>
.nav-drawer {
  z-index: 10001;
}
</style>

<script>
import { VNavigationDrawer, VList, VDivider, VListItem, VListItemAvatar, VListItemContent, VListItemTitle, VListItemIcon, VImg, VIcon } from 'vuetify/lib';
import { mapState } from 'vuex';
import Logo from './Logo.vue';

export default {
  name: 'NavDrawer',
  model: {
    prop: 'drawer',
    event: 'input'
  },
  props: ['drawer'],
  data: () => ({
    internalDrawer: false,
    /** @type {any[]} */ items: []
  }),
  computed: {
    ...mapState(['session']),
  },
  methods: {
    logout() {
      fetch('/api/session/logout.php')
        .then(res => res.json())
        .then(json => location.reload());
    },
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
            title: "Your Shops",
            icon: "mdi-shopping",
            path: "/your/shops/",
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
            title: "Logout",
            icon: "mdi-logout",
            action: () => this.logout(),
          },
        ]
      }
    }
  },
  components: {
    VNavigationDrawer,
    VList,
    VDivider,
    VListItem,
    VListItemAvatar,
    VListItemContent,
    VListItemTitle,
    VListItemIcon,
    VImg,
    VIcon,
    Logo
  },
};
</script>