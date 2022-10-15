<template>
  <VRow>
    <template v-if="products.length > 0">
      <VCol v-for="product in products" :key="product.id" cols="12" md="6">
        <VCard @click="() => $router.push('/product/' + product.id)">
          <VImg v-if="product.photos.length > 0" :src="'/api/photo/get.php?id=' + product.photos[0]" height="100" />
          <VImg v-else src="/images/placeholder.png" height="100" />
          <VCardTitle>{{product.name}}</VCardTitle>
          <VCardSubtitle>{{product.price}} - {{product.shopName}}</VCardSubtitle>
          <VCardText>{{product.description}}</VCardText>
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
  VRow, VCol, VSkeletonLoader, VCard, VCardTitle, VCardSubtitle, VCardText, VImg,
} from 'vuetify/lib';

export default {
  name: 'ProductList',
  props: ['products'],
  components: {
    VRow, VCol, VSkeletonLoader, VCard, VCardTitle, VCardSubtitle, VCardText, VImg,
  },
};
</script>
