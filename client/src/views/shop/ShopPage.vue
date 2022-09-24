<template>
  <div class="shop">
    <template v-if="shop">
      <VCard class="mb-10">
        <VRow>
          <VCol cols="12" md="4" order="1" order-md="12">
            <VImg src="/images/placeholder.png" height="250" />
          </VCol>
          <VCol cols="12" md="8" order="12" order-md="1">
            <VCard elevation="0">
              <VCardTitle>{{shop.name}}</VCardTitle>
              <VCardSubtitle>{{shop.address}} - {{shop.phoneNumber}}</VCardSubtitle>
              <VCardText>{{shop.description}}</VCardText>
              <VCardActions>
                <VRating hover size="30" half-increments readonly v-model="shop.rating"></VRating>
              </VCardActions>
            </VCard>
          </VCol>
        </VRow>
      </VCard>
      <VDivider></VDivider>
      <template v-if="session != null && session.role != 'visitor'">
        <h1>Rate it!</h1>
        <VRating class="mb-5" hover size="40" v-model="ownRating"></VRating>
      </template>
      <VDivider></VDivider>
      <h1>Comments</h1>
      <CommentTree :comments="comments"></CommentTree>
    </template>
    <template v-else>
      <VSkeletonLoader type="card"></VSkeletonLoader>
    </template>
  </div>
</template>

<script>
import { VRow, VCol, VSkeletonLoader, VRating, VDivider, VImg, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions } from 'vuetify/lib';
import CommentTree from '../../components/comments/CommentTree.vue';
import { mapState } from 'vuex';

export default {
  name: 'ShopPage',
  data: () => ({
    /** @type {any | null} */ shop: null,
    comments: [],
    ownRating: null
  }),
  computed: {
    ...mapState(['session']),
  },
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
  components: { VRow, VCol, VSkeletonLoader, VRating, VDivider, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VImg, CommentTree },
};
</script>
