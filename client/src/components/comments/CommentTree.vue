<template>
  <div class="comment-tree">
    <div v-if="!parentCommentId || replyOpen" class="comment-editor mb-4">
      <VTextarea v-model="content" :rules="[rules.required]" label="Comment"></VTextarea>
      <VBtn @click="publishComment">Publish</VBtn>
    </div>
    <div class="comment" v-for="comment in comments">
      <VCard v-if="comment.data.disapproved">
        <VCardText>A comment has been hidden because it has been disapproved by the community</VCardText>
      </VCard>
      <template v-else>
        <Comment :comment="comment.data" @reply="childrenReplyOpen = true"></Comment>
        <CommentTree :comments="comment.children" :itemType="itemType" :itemId="itemId"
          :parentCommentId="comment.data.id" :replyOpen="childrenReplyOpen" @reply="childrenReplyOpen = false"
          class="pl-10 my-4 inner-tree">
        </CommentTree>
      </template>
    </div>
  </div>
</template>

<style>
.inner-tree {
  border-left: 1px solid rgba(0, 0, 0, 0.1);
}
</style>

<script>
import { VTextarea, VBtn, VExpansionPanels, VExpansionPanel, VExpansionPanelHeader, VExpansionPanelContent } from 'vuetify/lib';
import Comment from './Comment.vue';
import { mapState } from 'vuex';

export default {
  name: 'CommentTree',
  props: ['comments', 'itemType', 'itemId', 'parentCommentId', 'replyOpen'],
  data: () => ({
    childrenReplyOpen: false,
    content: '',
    rules: {
      required: v => !!v || "Required",
    }
  }),
  computed: {
    ...mapState(['session']),
  },
  methods: {
    publishComment() {
      let body = {
        itemType: this.itemType,
        itemId: this.itemId,
        content: this.content,
        parentCommentId: this.parentCommentId
      };
      fetch("/api/comment/create.php", {
        method: "POST",
        body: JSON.stringify(body),
      })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.comments.unshift({
              data: {
                id: json.id,
                author: this.session.displayName,
                publishedAt: new Date(),
                content: this.content,
              },
              children: []
            });
            this.$emit("reply");
            this.content = "";
          }
        });
    }
  },
  components: { VTextarea, VBtn, VExpansionPanels, VExpansionPanel, VExpansionPanelHeader, VExpansionPanelContent, Comment }
}
</script>