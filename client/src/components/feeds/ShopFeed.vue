<template>
  <div class="feed">
    <VCard v-if="shops.length > 0">
      <VCarousel hide-delimiters height="400" cycle>
        <VCarouselItem class="mx-15" v-for="shop in shops" :key="shop.id">
          <VCard elevation="0">
            <VImg :src="LogoImg" height="200" />
            <VCardTitle>{{shop.name}}</VCardTitle>
            <VCardSubtitle>{{shop.address}} - {{shop.phoneNumber}}</VCardSubtitle>
            <VCardText>{{shop.shortDesc}}</VCardText>
            <VCardActions>
              <VBtn @click="() => $router.push('/shop/' + shop.id)">Visit page</VBtn>
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
  VRow, VCol, VSkeletonLoader, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VImg, VCarousel, VCarouselItem
} from 'vuetify/lib';
import LogoImg from '../../assets/logo.png';

export default {
  name: 'ShopList',
  props: ['shops'],
  data: () => ({
    LogoImg
  }),
  components: {
    VRow, VCol, VSkeletonLoader, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VImg, VCarousel, VCarouselItem
  },
};
</script>
