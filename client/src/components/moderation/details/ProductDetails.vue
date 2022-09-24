<template>
  <VCard>
    <VCardTitle>{{product.name}}</VCardTitle>
    <VCardSubtitle>{{product.price}} - {{product.shopName}}</VCardSubtitle>
    <VCardText>{{product.desc}}</VCardText>
    <VCardActions>
      <VBtn>Publish</VBtn>
      <VBtn>Reject</VBtn>
    </VCardActions>
  </VCard>
</template>

<script>
import { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon } from 'vuetify/lib';

export default {
  name: 'ProductDetails',
  props: ['product'],
  data: () => ({}),
  components: { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon }
}
</script>