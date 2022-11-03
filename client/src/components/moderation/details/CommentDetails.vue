<template>
  <VCard>
    <VCardTitle>{{ comment.author }}</VCardTitle>
    <VCardSubtitle>{{ comment.publishedAt }}</VCardSubtitle>
    <VCardText>
      {{ comment.content }}
      <VTextarea label="Reason" v-model="reason"></VTextarea>
    </VCardText>
    <VCardActions>
      <VBtn @click="$emit('publish', reason)">Publish</VBtn>
      <VBtn @click="$emit('reject', reason)">Reject</VBtn>
    </VCardActions>
  </VCard>
</template>

<script>
import { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon, VTextarea } from 'vuetify/lib';

export default {
  name: 'CommentDetails',
  props: ['comment'],
  data: () => ({
    reason: '',
  }),
  components: { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon, VTextarea }
}
</script>