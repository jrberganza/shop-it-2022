<template>
  <div class="shop">
    <template v-if="shop">
      <VCard class="mb-10">
        <VRow no-gutters>
          <VCol cols="12" md="4" order="1" order-md="3">
            <VImg v-if="shop.photos.length > 0" :src="'/api/photo/get.php?id=' + shop.photos[0]" height="250" />
            <VImg v-else src="/images/placeholder.png" height="250" />
          </VCol>
          <VCol cols="12" md="8" order="2" order-md="2">
            <VCard elevation="0">
              <VCardTitle>{{ shop.name }}</VCardTitle>
              <VCardSubtitle>
                Zona {{ shop.zone }}, {{ shop.municipality }}, {{ shop.department }} - {{ shop.phoneNumber }}
                <VChipGroup column>
                  <VChip v-for="category in shop.categories" :key="category.id">
                    {{ category.name }}
                  </VChip>
                </VChipGroup>
              </VCardSubtitle>
              <VCardText>{{ shop.description }}</VCardText>
              <VCardActions>
                <VRating hover size="30" half-increments readonly v-model="shop.rating"></VRating>
              </VCardActions>
            </VCard>
          </VCol>
          <VCol cols="12" order="3" order-md="1">
            <Map :location="[shop.latitude, shop.longitude]"></Map>
          </VCol>
        </VRow>
      </VCard>
      <VDivider></VDivider>
      <h1>Products</h1>
      <VDataIterator class="my-2" :items="shop.products" :itemsPerPage="12">
        <template v-slot:default="{ items }">
          <VRow>
            <VCol v-for="product in items" :key="product.id" cols="12" sm="6" md="4" lg="3">
              <VCard @click="() => $router.push('/product/' + product.id)">
                <VCardTitle>{{ product.name }}</VCardTitle>
                <VCardSubtitle>{{ product.price }} - {{ product.shopName }}</VCardSubtitle>
                <VCardText>{{ product.description }}</VCardText>
              </VCard>
            </VCol>
          </VRow>
        </template>
      </VDataIterator>
      <template v-if="session != null && session.role != 'visitor'">
        <VDivider></VDivider>
        <h1>Rate it!</h1>
        <VRating class="mb-5" hover size="40" v-model="shop.ownRating"></VRating>
      </template>
      <VDivider></VDivider>
      <h1>Comments</h1>
      <CommentTree :comments="comments" itemType="shop" :itemId="parseInt(this.$route.params.id, 10)"></CommentTree>
    </template>
    <template v-else>
      <VSkeletonLoader type="card"></VSkeletonLoader>
    </template>
  </div>
</template>

<script>
import { VRow, VCol, VSkeletonLoader, VRating, VDivider, VImg, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VDataIterator } from 'vuetify/lib';
import CommentTree from '../../components/comments/CommentTree.vue';
import Map from '../../components/map/Map.vue';
import { mapState, mapMutations } from 'vuex';

export default {
  name: 'ShopPage',
  data: () => ({
    /** @type {any | null} */ shop: null,
    comments: []
  }),
  computed: {
    ...mapState(['session']),
  },
  watch: {
    ['shop.ownRating'](newVal) {
      fetch(`/api/rating/shop/rate.php`, {
        method: "POST",
        body: JSON.stringify({ id: this.$route.params.id, rating: newVal }),
      })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            // Nothing
          } else {
            this.openSnackbar({ shown: true, message: json._error });
          }
        });
    }
  },
  methods: {
    getShop(id) {
      fetch(`/api/shop/get.php?id=${id}`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.shop = json
          } else {
            this.openSnackbar({ shown: true, message: json._error });
          }
        });
    },
    loadComments(id) {
      fetch(`/api/comment/shop/all.php?id=${id}`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.comments = json.comments
          } else {
            this.openSnackbar({ shown: true, message: json._error });
          }
        });
    },
    ...mapMutations(['openSnackbar']),
  },
  mounted() {
    this.getShop(this.$route.params.id);
    this.loadComments(this.$route.params.id);
  },
  components: { VRow, VCol, VSkeletonLoader, VRating, VDivider, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VImg, VDataIterator, CommentTree, Map },
};
</script>
