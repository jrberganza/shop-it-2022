<template>
  <div class="shop">
    <template v-if="shop">
      <VRow>
        <VCol cols="12" md="8">
          <h1>{{shop.name}}</h1>
          <p>{{shop.address}} - {{shop.phoneNumber}}</p>
          <p>{{shop.shortDesc}}</p>
        </VCol>
        <VCol cols="12" md="4">
          <img src="@/assets/logo.png" width="100%" height="100%" /> <!-- TODO: Change to map -->
        </VCol>
      </VRow>
      <CommentTree :comments="comments"></CommentTree>
    </template>
    <template v-else>
      <VSkeletonLoader type="card"></VSkeletonLoader>
    </template>
  </div>
</template>

<script>
import { VRow, VCol, VSkeletonLoader } from 'vuetify/lib';
import CommentTree from '../../components/comments/CommentTree.vue';

export default {
  name: 'ShopPage',
  data: () => ({
    /** @type {any | null} */ shop: null,
    comments: []
  }),
  methods: {
    getShop(id) {
      fetch(`/api/shop/get.php?id=${id}`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.shop = json
          } else {
            this.$router.replace(`/shop/${id}/notfound`);
          }
        });
    },
    loadComments(id) {
      fetch(`/api/comment/shop/all.php?id=${id}`)
        .then(res => res.json())
        .then(json => this.comments = json);
    },
  },
  mounted() {
    this.getShop(this.$route.params.id);
    this.loadComments(this.$route.params.id);
  },
  components: { VRow, VCol, VSkeletonLoader, CommentTree },
};
</script>
