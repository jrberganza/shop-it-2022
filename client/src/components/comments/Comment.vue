<template>
  <VCard>
    <VCardTitle>{{comment.author}}</VCardTitle>
    <VCardSubtitle>{{comment.publishedAt}}</VCardSubtitle>
    <VCardText>{{comment.content}}</VCardText>
    <VCardActions>
      <VBtn icon>
        <VIcon>mdi-arrow-up</VIcon>
      </VBtn>
      <VBtn icon>
        <VIcon>mdi-arrow-down</VIcon>
      </VBtn>
    </VCardActions>
  </VCard>
</template>

<script>
import { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon } from 'vuetify/lib';

export default {
  name: 'CommentTree',
  props: ['comment'],
  data: () => ({

  }),
  components: { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon }
}
</script>