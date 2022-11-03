<template>
  <VCard>
    <VImg v-if="shop.photos.length > 0" :src="'/api/photo/get.php?id=' + shop.photos[0]" height="250" />
    <VImg v-else src="/images/placeholder.png" height="250" />
    <VCardTitle>{{ shop.name }}</VCardTitle>
    <VCardSubtitle>Zona {{ shop.zone }}, {{ shop.municipality }}, {{ shop.department }} - {{ shop.phoneNumber }}
    </VCardSubtitle>
    <VCardText>{{ shop.description }}</VCardText>
    <VCardText v-if="!shop.moderatable">Please publish or reject all pending products from this shop</VCardText>
    <VCardActions v-else>
      <VBtn @click="$emit('publish')">Publish</VBtn>
      <VBtn @click="$emit('reject')">Reject</VBtn>
    </VCardActions>
  </VCard>
</template>

<script>
import { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon, VImg } from 'vuetify/lib';

export default {
  name: 'ShopDetails',
  props: ['shop'],
  data: () => ({}),
  components: { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon, VImg }
}
</script>