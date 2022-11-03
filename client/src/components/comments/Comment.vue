<template>
  <VCard>
    <VCardTitle>{{ comment.author }}</VCardTitle>
    <VCardSubtitle>{{ comment.publishedAt }}</VCardSubtitle>
    <VCardText>{{ comment.content }}</VCardText>
    <VCardText v-if="!comment.moderated">
      This comment hasn't been checked by moderators
    </VCardText>
    <VCardActions v-else>
      <VBtn icon @click="upvote  " :disabled="  session.role == 'visi  tor'">
        <VIcon :color="comment.voted == 1 ?   'primary' : 'default'">mdi-arrow-up</VIcon>
      </VBtn>
      <span>{{ comment.totalVotes }}</span>
      <VBtn icon @click="downvote" :disabled="session.role == 'visitor'">
        <VIcon :color="comment.voted == -1 ? 'primary' : 'default'">mdi-arrow-down</VIcon>
      </VBtn>
      <VBtn v-if="session.role != 'visitor'" text @click="$emit('askToReply')">
        Reply
      </VBtn>
    </VCardActions>
  </VCard>
</template>

<script>
import { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon } from 'vuetify/lib';
import { mapState, mapMutations } from 'vuex';

export default {
  name: 'CommentTree',
  props: ['comment'],
  data: () => ({}),
  computed: {
    ...mapState(['session']),
  },
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
            this.comment.totalVotes -= this.comment.voted;
            this.comment.voted = newVote;
            this.comment.totalVotes += this.comment.voted;
          } else {
            this.openSnackbar({ shown: true, message: json._error });
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
            this.comment.totalVotes -= this.comment.voted;
            this.comment.voted = newVote;
            this.comment.totalVotes += this.comment.voted;
          } else {
            this.openSnackbar({ shown: true, message: json._error });
          }
        });
    },
    ...mapMutations(['openSnackbar']),
  },
  components: { VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VBtn, VIcon }
}
</script>