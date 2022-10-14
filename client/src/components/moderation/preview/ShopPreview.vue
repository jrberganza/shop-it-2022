<template>
  <VCard>
    <VImg v-if="shop.photos.length > 0" :src="'/api/photo/get.php?id=' + shop.photos[0]" height="100" />
    <VImg v-else src="/images/placeholder.png" height="100" />
    <VCardTitle>{{shop.name}}</VCardTitle>
    <VCardSubtitle>{{shop.address}} - {{shop.phoneNumber}}</VCardSubtitle>
    <VCardText>{{shop.shortDesc}}</VCardText>
    <VCardActions>
      <VBtn @click="() => $emit('seeDetails', shop.id)">Details</VBtn>
      <VBtn>Publish</VBtn>
      <VBtn>Reject</VBtn>
    </VCardActions>
  </VCard>
</template>

<script>
import { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon, VImg } from 'vuetify/lib';

export default {
  name: 'ShopPreview',
  props: ['shop'],
  components: { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon, VImg }
}
</script>