<template>
  <VFooter color="primary lighten-1" padless>
    <VRow justify="center" no-gutters>
      <VBtn v-for="link in links" :key="link.title" dark text class="my-2" @click="$router.push(link.path)">
        {{ link.title }}
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

export default {
  name: 'PageFooter',
  data: () => ({
    links: [
      {
        title: 'Homepage',
        path: '/',
      }
    ]
  }),
  components: {
    VFooter, VRow, VCol, VBtn
  },
};
</script>
