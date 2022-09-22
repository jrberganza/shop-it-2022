<template>
  <div class="product">
    <template v-if="product">
      <h1>{{product.name}}</h1>
      <p>{{product.price}} - {{product.shopName}}</p>
      <p>{{product.shortDesc}}</p>

      <CommentTree :comments="comments"></CommentTree>
    </template>
    <template v-else>
      <VSkeletonLoader type="card"></VSkeletonLoader>
    </template>
  </div>
</template>

<script>
import CommentTree from '../../components/comments/CommentTree.vue';

export default {
  name: 'ProductPage',
  data: () => ({
    /** @type {any | null} */ product: null,
    comments: []
  }),
  methods: {
    getShop(id) {
      fetch(`/api/product/get.php?id=${id}`)
        .then(res => res.json())
        .then(json => this.product = json);
    },
    loadComments(id) {
      fetch(`/api/comment/product/all.php?id=${id}`)
        .then(res => res.json())
        .then(json => this.comments = json);
    },
  },
  mounted() {
    this.getShop(this.$route.params.id);
    this.loadComments(this.$route.params.id);
  },
  components: { CommentTree },
};
</script>
