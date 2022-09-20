<template>
  <VToolbar class="toolbar">
    <VToolbarTitle class="logo">ShopIt!</VToolbarTitle>
    <VRow>
      <VSpacer></VSpacer>
      <VCol cols="8">
        <VTextField v-model="searchTerm" hide-details @keydown="searchOnEnter">
          <template v-slot:append>
            <VBtn icon small @click="search">
              <VIcon>mdi-magnify</VIcon>
            </VBtn>
          </template>
        </VTextField>
      </VCol>
      <VSpacer></VSpacer>
    </VRow>
    <VBtn>Sign in</VBtn>
  </VToolbar>
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
  VToolbar, VToolbarTitle, VTextField, VBtn, VIcon, VRow, VCol, VSpacer,
} from 'vuetify/lib';

export default {
  name: 'PageHeader',
  data: () => ({
    searchTerm: '',
  }),
  methods: {
    search() {
      this.$router.push(`/search/?q=${encodeURIComponent(this.searchTerm)}`);
    },
    searchOnEnter(ev) {
      if (ev.key.toLowerCase() === 'enter') {
        this.search();
      }
    },
  },
  components: {
    VToolbar, VToolbarTitle, VTextField, VBtn, VIcon, VRow, VCol, VSpacer,
  },
};
</script>
