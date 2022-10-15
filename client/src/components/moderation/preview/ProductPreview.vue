<template>
  <VCard>
    <VImg v-if="product.photos.length > 0" :src="'/api/photo/get.php?id=' + product.photos[0]" height="100" />
    <VImg v-else src="/images/placeholder.png" height="100" />
    <VCardTitle>{{product.name}}</VCardTitle>
    <VCardSubtitle>{{product.price}} - {{product.shopName}}</VCardSubtitle>
    <VCardText>{{product.description}}</VCardText>
    <VCardActions>
      <VBtn @click="() => $emit('seeDetails', product.id)">Details</VBtn>
      <VBtn>Publish</VBtn>
      <VBtn>Reject</VBtn>
    </VCardActions>
  </VCard>
</template>

<script>
import { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VImg, VIcon } from 'vuetify/lib';

export default {
  name: 'ProductPreview',
  props: ['product'],
  data: () => ({}),
  components: { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VImg, VIcon }
}
</script>