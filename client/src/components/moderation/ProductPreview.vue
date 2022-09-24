<template>
  <VCard>
    <VCardTitle>{{product.name}}</VCardTitle>
    <VCardSubtitle>{{product.price}} - {{product.shopName}}</VCardSubtitle>
    <VCardText>{{product.shortDesc}}</VCardText>
  </VCard>
</template>

<script>
import { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon } from 'vuetify/lib';

export default {
  name: 'ProductPreview',
  props: ['product'],
  data: () => ({}),
  components: { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon }
}
</script>