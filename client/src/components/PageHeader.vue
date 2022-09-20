<template>
  <VToolbar class="toolbar">
    <div class="logo">ShopIt!</div>
    <VRow>
      <VSpacer></VSpacer>
      <VCol cols="8">
        <VTextField hide-details>
          <template v-slot:append>
            <VBtn icon small>
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
  VToolbar, VTextField, VBtn, VIcon, VRow, VCol, VSpacer,
} from 'vuetify/lib';

export default {
  name: 'PageHeader',
  components: {
    VToolbar, VTextField, VBtn, VIcon, VRow, VCol, VSpacer,
  },
};
</script>
