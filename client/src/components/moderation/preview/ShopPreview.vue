<template>
  <VCard>
    <VImg v-if="shop.photos.length > 0" :src="'/api/photo/get.php?id=' + shop.photos[0]" height="100" />
    <VImg v-else src="/images/placeholder.png" height="100" />
    <VCardTitle>{{ shop.name }}</VCardTitle>
    <VCardSubtitle>Zona {{ shop.zone }}, {{ shop.municipality }}, {{ shop.department }} - {{ shop.phoneNumber }}
    </VCardSubtitle>
    <VCardText>{{ shop.description }}</VCardText>
    <VCardActions>
      <VBtn @click="() => $emit('seeDetails')">Details</VBtn>
      <!-- <template v-if="shop.moderatable"> -->
      <VBtn @click="$emit('publish')">Publish</VBtn>
      <VBtn @click="$emit('reject')">Reject</VBtn>
      <!-- </template> -->
    </VCardActions>
    <!-- <VCardText v-if="!shop.moderatable">Please publish or reject all pending products from this shop</VCardText> -->
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