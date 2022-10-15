<template>
  <VRow>
    <template v-if="shops.length > 0">
      <VCol v-for="shop in shops" :key="shop.id" cols="12" md="6">
        <VCard @click="() => $router.push('/shop/' + shop.id)">
          <VImg v-if="shop.photos.length > 0" :src="'/api/photo/get.php?id=' + shop.photos[0]" height="100" />
          <VImg v-else src="/images/placeholder.png" height="100" />
          <VCardTitle>{{shop.name}}</VCardTitle>
          <VCardSubtitle>{{shop.address}} - {{shop.phoneNumber}}</VCardSubtitle>
          <VCardText>{{shop.description}}</VCardText>
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
