<template>
  <VCard>
    <VCardTitle>{{comment.author}}</VCardTitle>
    <VCardSubtitle>{{comment.publishedAt}}</VCardSubtitle>
    <VCardText>{{comment.content}}</VCardText>
    <VCardActions>
      <VBtn icon @click="upvote">
        <VIcon :color="comment.voted == 1 ? 'primary' : 'default'">mdi-arrow-up</VIcon>
      </VBtn>
      <VBtn icon @click="downvote">
        <VIcon :color="comment.voted == -1 ? 'primary' : 'default'">mdi-arrow-down</VIcon>
      </VBtn>
      <VBtn text @click="$emit('reply')">
        Reply
      </VBtn>
    </VCardActions>
  </VCard>
</template>

<script>
import { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon } from 'vuetify/lib';

export default {
  name: 'CommentTree',
  props: ['comment'],
  data: () => ({}),
  methods: {
    upvote() {
      let newVote = this.comment.voted == 1 ? 0 : 1;
      fetch("/api/comment/vote.php", {
        "method": "POST",
        "body": JSON.stringify({ id: this.comment.id, value: newVote }),
      })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.comment.voted = newVote;
          }
        });
    },
    downvote() {
      let newVote = this.comment.voted == -1 ? 0 : -1;
      fetch("/api/comment/vote.php", {
        "method": "POST",
        "body": JSON.stringify({ id: this.comment.id, value: newVote }),
      })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.comment.voted = newVote;
          }
        });
    },
  },
  components: { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon }
}
</script>