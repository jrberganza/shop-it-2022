<template>
  <VCard>
    <VImg :src="LogoImg" height="300" /> <!-- TODO: Change to map -->
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
import LogoImg from '../../../assets/logo.png';

export default {
  name: 'ShopDetails',
  props: ['shop'],
  data: () => ({
    LogoImg
  }),
  components: { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon, VImg }
}
</script>