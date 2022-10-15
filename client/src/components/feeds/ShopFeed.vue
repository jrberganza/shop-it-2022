<template>
  <div class="feed">
    <VCard v-if="shops.length > 0">
      <VCarousel hide-delimiters height="350" cycle>
        <VCarouselItem v-for="shop in shops" :key="shop.id">
          <VCard class="px-15" elevation="0" height="100%" @click="() => $router.push('/shop/' + shop.id)">
            <VImg v-if="shop.photos.length > 0" :src="'/api/photo/get.php?id=' + shop.photos[0]" height="200" />
            <VImg v-else src="/images/placeholder.png" height="200" />
            <VCardTitle>{{shop.name}}</VCardTitle>
            <VCardSubtitle>{{shop.address}} - {{shop.phoneNumber}}</VCardSubtitle>
            <VCardText>{{shop.description}}</VCardText>
          </VCard>
        </VCarouselItem>
      </VCarousel>
    </VCard>
    <VSkeletonLoader v-else type="card"></VSkeletonLoader>
  </div>
</template>

<script>
import {
  VRow, VCol, VSkeletonLoader, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VImg, VCarousel, VCarouselItem
} from 'vuetify/lib';

export default {
  name: 'ShopList',
  props: ['shops'],
  components: {
    VRow, VCol, VSkeletonLoader, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VImg, VCarousel, VCarouselItem
  },
};
</script>
