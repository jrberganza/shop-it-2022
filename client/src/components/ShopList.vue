<template>
  <VRow>
    <template v-if="shops.length > 0">
      <VCol v-for="shop in shops" :key="shop.id" cols="4">
        <VCard @click="() => $router.push('/shop/' + shop.id)">
          <img src="../assets/logo.png" width="100%" height="100%" /> <!-- TODO: Change to map -->
          <VCardTitle>{{shop.name}}</VCardTitle>
          <VCardSubtitle>{{shop.address}} - {{shop.phoneNumber}}</VCardSubtitle>
          <VCardText>{{shop.shortDesc}}</VCardText>
        </VCard>
      </VCol>
    </template>
    <template v-else>
      <VCol v-for="i in Array(3 * 10)" :key="i" cols="4">
        <VSkeletonLoader type="card"></VSkeletonLoader>
      </VCol>
    </template>
  </VRow>
</template>

<script>
import {
  VRow, VCol, VSkeletonLoader, VCard, VCardTitle, VCardSubtitle, VCardText, VImg
} from 'vuetify/lib';

export default {
  name: 'ShopList',
  props: ['shops'],
  components: {
    VRow, VCol, VSkeletonLoader, VCard, VCardTitle, VCardSubtitle, VCardText, VImg
  },
};
</script>
