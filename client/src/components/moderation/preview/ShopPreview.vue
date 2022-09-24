<template>
  <VCard>
    <VImg :src="LogoImg" height="100" /> <!-- TODO: Change to map -->
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
import LogoImg from '../../../assets/logo.png';

export default {
  name: 'ShopPreview',
  props: ['shop'],
  data: () => ({
    LogoImg
  }),
  components: { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon, VImg }
}
</script>