<template>
  <VCard>
    <VCardTitle>{{ comment.author }}</VCardTitle>
    <VCardSubtitle>{{ comment.publishedAt }}</VCardSubtitle>
    <VCardText>{{ comment.content }}</VCardText>
    <VCardActions>
      <VBtn @click="$emit('publish')">Publish</VBtn>
      <VBtn @click="$emit('reject')">Reject</VBtn>
    </VCardActions>
  </VCard>
</template>

<script>
import { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon } from 'vuetify/lib';

export default {
  name: 'CommentDetails',
  props: ['comment'],
  data: () => ({}),
  components: { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon }
}
</script>