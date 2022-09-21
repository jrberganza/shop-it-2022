<template>
  <div class="comment-tree">
    <div class="comment" v-for="comment in comments">
      <Comment :comment="comment.data"></Comment>
      <CommentTree :comments="comment.children" class="pl-10 my-4 inner-tree"></CommentTree>
    </div>
  </div>
</template>

<style>
.inner-tree {
  border-left: 1px solid rgba(0, 0, 0, 0.1);
}
</style>

<script>
import Comment from './Comment.vue';

export default {
  name: 'CommentTree',
  props: ['comments'],
  data: () => ({

  }),
  components: { Comment }
}
</script>