<template>
  <VRow>
    <template v-if="products.length > 0">
      <VCol v-for="product in products" :key="product.id" cols="4">
        <VCard @click="() => $router.push('/product/' + product.id)">
          <VCardTitle>{{product.name}}</VCardTitle>
          <VCardSubtitle>{{product.price}} - {{product.shopName}}</VCardSubtitle>
          <VCardText>{{product.shortDesc}}</VCardText>
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
  VRow, VCol, VSkeletonLoader, VCard, VCardTitle, VCardSubtitle, VCardText,
} from 'vuetify/lib';

export default {
  name: 'ProductList',
  props: ['products'],
  components: {
    VRow, VCol, VSkeletonLoader, VCard, VCardTitle, VCardSubtitle, VCardText,
  },
};
</script>
