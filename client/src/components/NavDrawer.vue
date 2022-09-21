<template>
  <VNavigationDrawer v-model="internalDrawer" @input="() => $emit('input', internalDrawer)" absolute temporary
    color="primary" dark class="nav-drawer">
    <VListItem>
      <VListItemContent>
        <VListItemTitle>ShopIt!</VListItemTitle>
      </VListItemContent>
    </VListItem>

    <VDivider></VDivider>

    <VList dense>
      <VListItem v-for="item in items" :key="item.title" link @click="$router.push(item.path)">
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

export default {
  name: 'NavDrawer',
  model: {
    prop: 'drawer',
    event: 'input'
  },
  props: ['drawer'],
  data: () => ({
    internalDrawer: false,
    items: [
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
  }),
  watch: {
    drawer(newVal) {
      this.internalDrawer = newVal;
    }
  },
  components: {
    VNavigationDrawer, VList, VDivider, VListItem, VListItemAvatar, VListItemContent, VListItemTitle, VListItemIcon, VImg, VIcon
  },
};
</script>