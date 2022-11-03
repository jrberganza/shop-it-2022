<template>
  <VCard>
    <VImg v-if="product.photos.length > 0" :src="'/api/photo/get.php?id=' + product.photos[0]" height="250" />
    <VImg v-else src="/images/placeholder.png" height="250" />
    <VCardTitle>{{ product.name }}</VCardTitle>
    <VCardSubtitle>{{ product.price }} - {{ product.shopName }}</VCardSubtitle>
    <VCardText>
      {{ product.description }}
      <VTextarea label="Reason" v-model="reason"></VTextarea>
    </VCardText>
    <VCardActions>
      <VBtn @click="$emit('publish', reason)">Publish</VBtn>
      <VBtn @click="$emit('reject', reason)">Reject</VBtn>
    </VCardActions>
  </VCard>
</template>

<script>
import { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VImg, VIcon, VTextarea } from 'vuetify/lib';

export default {
  name: 'ProductDetails',
  props: ['product'],
  data: () => ({
    reason: '',
  }),
  components: { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VImg, VIcon, VTextarea }
}
</script>