<template>
  <VCard>
    <VImg src="/images/placeholder.png" height="300" />
    <VCardTitle>{{shop.name}}</VCardTitle>
    <VCardSubtitle>{{shop.address}} - {{shop.phoneNumber}}</VCardSubtitle>
    <VCardText>{{shop.desc}}</VCardText>
    <VCardActions>
      <VBtn>Publish</VBtn>
      <VBtn>Reject</VBtn>
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