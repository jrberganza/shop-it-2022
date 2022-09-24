<template>
  <div class="feed">
    <VCard v-if="products.length > 0">
      <VCarousel hide-delimiters height="400" cycle>
        <VCarouselItem class="mx-15" v-for="product in products" :key="product.id">
          <VCard elevation="0">
            <VCardTitle>{{product.name}}</VCardTitle>
            <VCardSubtitle>{{product.price}} - {{product.shopName}}</VCardSubtitle>
            <VCardText>{{product.shortDesc}}</VCardText>
            <VCardActions>
              <VBtn @click="() => $router.push('/product/' + product.id)">Visit page</VBtn>
            </VCardActions>
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
