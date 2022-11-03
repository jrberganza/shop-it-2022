<template>
  <div class="product">
    <template v-if="product">
      <VCard class="mb-10">
        <VRow>
          <VCol cols="12" md="4" order="1" order-md="12">
            <VImg v-if="product.photos.length > 0" :src="'/api/photo/get.php?id=' + product.photos[0]" height="250" />
            <VImg v-else src="/images/placeholder.png" height="250" />
          </VCol>
          <VCol cols="12" md="8" order="12" order-md="1">
            <VCard elevation="0">
              <VCardTitle>{{ product.name }}</VCardTitle>
              <VCardSubtitle>
                {{ product.price }} - <RouterLink :to="'/shop/' + product.shopId">{{ product.shopName }}</RouterLink>
                <VChipGroup column>
                  <VChip v-for="category in product.categories" :key="category.id">
                    {{ category.name }}
                  </VChip>
                </VChipGroup>
              </VCardSubtitle>
              <VCardText>{{ product.description }}</VCardText>
              <VCardActions>
                <VRating hover size="30" half-increments readonly v-model="product.rating"></VRating>
              </VCardActions>
            </VCard>
          </VCol>
        </VRow>
      </VCard>
      <template v-if="session != null && session.role != 'visitor'">
        <VDivider></VDivider>
        <h1>Rate it!</h1>
        <VRating class="mb-5" hover size="40" v-model="product.ownRating"></VRating>
      </template>
      <VDivider></VDivider>
      <h1>Comments</h1>
      <CommentTree :comments="comments" itemType="product" :itemId="parseInt(this.$route.params.id, 10)"></CommentTree>
    </template>
    <template v-else>
      <VSkeletonLoader type="card"></VSkeletonLoader>
    </template>
  </div>
</template>

<script>
import { VRow, VCol, VSkeletonLoader, VRating, VDivider, VImg, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VDataIterator } from 'vuetify/lib';
import CommentTree from '../../components/comments/CommentTree.vue';
import { mapState } from 'vuex';
import { RouterLink } from 'vue-router';

export default {
  name: 'ProductPage',
  data: () => ({
    /** @type {any | null} */ product: null,
    comments: [],
  }),
  computed: {
    ...mapState(['session']),
  },
  watch: {
    ['product.ownRating'](newVal) {
      fetch(`/api/rating/product/rate.php`, {
        method: "POST",
        body: JSON.stringify({ id: this.$route.params.id, rating: newVal }),
      })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            // Nothing
          }
        });
    }
  },
  methods: {
    getShop(id) {
      fetch(`/api/product/get.php?id=${id}`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.product = json
          }
        });
    },
    loadComments(id) {
      fetch(`/api/comment/product/all.php?id=${id}`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.comments = json.comments
          }
        });
    },
  },
  mounted() {
    this.getShop(this.$route.params.id);
    this.loadComments(this.$route.params.id);
  },
  components: { VRow, VCol, VSkeletonLoader, VRating, VDivider, VImg, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VDataIterator, CommentTree, RouterLink },
};
</script>
