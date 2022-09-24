<template>
  <div class="dashboard">
    <VRow>
      <VCol cols="12" md="6" lg="4">
        <VDataIterator :items="shops" items-per-page="5">
          <template v-slot:default="{ items }">
            <ShopPreview class="my-2" v-for="shop in items" :key="shop.id" :shop="shop"></ShopPreview>
          </template>
        </VDataIterator>
      </VCol>
      <VCol cols="12" md="6" lg="4">
        <VDataIterator :items="products" items-per-page="5">
          <template v-slot:default="{ items }">
            <ProductPreview class="my-2" v-for="product in items" :key="product.id" :product="product"></ProductPreview>
          </template>
        </VDataIterator>
      </VCol>
      <VCol cols="12" lg="4">
        <VDataIterator :items="comments" items-per-page="5">
          <template v-slot:default="{ items }">
            <CommentPreview class="my-2" v-for="comment in items" :key="comment.id" :comment="comment"></CommentPreview>
          </template>
        </VDataIterator>
      </VCol>
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol, VDataIterator, VCard } from 'vuetify/lib';
import CommentPreview from '../../components/moderation/CommentPreview.vue';
import ProductPreview from '../../components/moderation/ProductPreview.vue';
import ShopPreview from '../../components/moderation/ShopPreview.vue';

export default {
  name: 'Dashboard',
  data: () => ({
    /** @type {any[]} */ shops: [],
    /** @type {any[]} */ products: [],
    /** @type {any[]} */ comments: [],
  }),
  methods: {
    getPendingShops() {
      fetch('/api/shop/moderation/pending.php')
        .then(res => res.json())
        .then(json => this.shops = json);
    },
    getPendingProducts() {
      fetch('/api/product/moderation/pending.php')
        .then(res => res.json())
        .then(json => this.products = json);
    },
    getPendingComments() {
      fetch('/api/comment/moderation/pending.php')
        .then(res => res.json())
        .then(json => this.comments = json);
    },
  },
  mounted() {
    this.getPendingShops();
    this.getPendingProducts();
    this.getPendingComments();
  },
  components: { VRow, VCol, VDataIterator, VCard, CommentPreview, ProductPreview, ShopPreview }
}
</script>