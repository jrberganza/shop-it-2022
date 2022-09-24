<template>
  <div class="feed">
    <VCard v-if="products.length > 0">
      <VCarousel hide-delimiters height="350" cycle>
        <VCarouselItem v-for="product in products" :key="product.id">
          <VCard class="px-15" elevation="0" height="100%" @click="() => $router.push('/product/' + product.id)">
            <VCardTitle>{{product.name}}</VCardTitle>
            <VCardSubtitle>{{product.price}} - {{product.shopName}}</VCardSubtitle>
            <VCardText>{{product.shortDesc}}</VCardText>
          </VCard>
        </VCarouselItem>
      </VCarousel>
    </VCard>
    <VSkeletonLoader v-else type="card"></VSkeletonLoader>
  </div>
</template>

<script>
import {
  VRow, VCol, VSkeletonLoader, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VCarousel, VCarouselItem
} from 'vuetify/lib';

export default {
  name: 'ProductList',
  props: ['products'],
  components: {
    VRow, VCol, VSkeletonLoader, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VCarousel, VCarouselItem
  },
};
</script>
