<template>
  <VRow class="px-4 my-2">
    <template v-if="shops.length > 0">
      <VCol v-for="shop in shops" :key="shop.id" cols="12" md="6">
        <VCard @click="() => $router.push('/shop/' + shop.id)">
          <VImg v-if="shop.photos.length > 0" :src="'/api/photo/get.php?id=' + shop.photos[0]" height="100" />
          <VImg v-else src="/images/placeholder.png" height="100" />
          <VCardTitle>{{ shop.name }}</VCardTitle>
          <VCardSubtitle>Zona {{ shop.zone }}, {{ shop.municipality }}, {{ shop.department }} - {{ shop.phoneNumber }}
          </VCardSubtitle>
          <VCardText>{{ shop.description }}</VCardText>
        </VCard>
      </VCol>
    </template>
    <div v-else>
      <em>No results found.</em>
    </div>
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
