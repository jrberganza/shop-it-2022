<template>
  <div class="nav-drawer-absolute" :class="{ 'nav-drawer-hidden': !internalDrawer }">
    <div class="nav-drawer-sticky">
      <VNavigationDrawer v-model="internalDrawer" @input="() => $emit('input', internalDrawer)" temporary
        color="primary" dark>
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
    </div>
  </div>
</template>

<style>
.nav-drawer-hidden {
  width: 0px;
}

.nav-drawer-absolute {
  position: absolute;
  top: 0;
  height: 100%;
  z-index: 10001;
}

.nav-drawer-sticky {
  position: sticky;
  top: 0;
  height: 100vh;
}
</style>

<script>
import { VNavigationDrawer, VList, VDivider, VListItem, VListItemAvatar, VListItemContent, VListItemTitle, VListItemIcon, VImg, VIcon } from 'vuetify/lib';
import { mapMutations, mapState } from 'vuex';
import { getNavItems } from '../navItems';
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