<template>
  <div class="dashboard">
    <VRow>
      <VCol cols="12" lg="6" order="1" order-lg="12">
        <ShopDetails v-if="selected.type == 'shop'" :shop="selected.data"></ShopDetails>
        <ProductDetails v-else-if="selected.type == 'product'" :product="selected.data"></ProductDetails>
        <CommentDetails v-else-if="selected.type == 'comment'" :comment="selected.data"></CommentDetails>
        <p v-else><em>No item has been selected</em></p>
      </VCol>
      <VCol cols="12" lg="6" order="12" order-lg="1">
        <VTabs v-model="tab">
          <VTab v-for="tab in tabs" :key="tab">
            {{ tab }}
          </VTab>
        </VTabs>
        <VTabsItems v-model="tab">
          <VTabItem class="ma-2">
            <VDataIterator :items="shops" :items-per-page="5">
              <template v-slot:default="{ items }">
                <ShopPreview class="my-2" v-for="shop in items" :key="shop.id" :shop="shop" @seeDetails="selectShop">
                </ShopPreview>
              </template>
            </VDataIterator>
          </VTabItem>
          <VTabItem class="ma-2">
            <VDataIterator :items="products" :items-per-page="5">
              <template v-slot:default="{ items }">
                <ProductPreview class="my-2" v-for="product in items" :key="product.id" :product="product"
                  @seeDetails="selectProduct"></ProductPreview>
              </template>
            </VDataIterator>
          </VTabItem>
          <VTabItem class="ma-2">
            <VDataIterator :items="comments" :items-per-page="5">
              <template v-slot:default="{ items }">
                <CommentPreview class="my-2" v-for="comment in items" :key="comment.id" :comment="comment"
                  @seeDetails="selectComment"></CommentPreview>
              </template>
            </VDataIterator>
          </VTabItem>
        </VTabsItems>
      </VCol>
    </VRow>
  </div>
</template>

<script>
import { VCol, VRow, VTabs, VTab, VTabsItems, VTabItem, VDataIterator, VCard } from 'vuetify/lib';
import ShopPreview from '../../components/moderation/preview/ShopPreview.vue';
import ProductPreview from '../../components/moderation/preview/ProductPreview.vue';
import CommentPreview from '../../components/moderation/preview/CommentPreview.vue';
import ShopDetails from '../../components/moderation/details/ShopDetails.vue';
import ProductDetails from '../../components/moderation/details/ProductDetails.vue';
import CommentDetails from '../../components/moderation/details/CommentDetails.vue';

export default {
  name: 'Dashboard',
  data: () => ({
    selected: {
      /** @type {any | null} */ type: null,
      data: {}
    },
    tabs: ['Shops', 'Products', 'Comments'],
    tab: null,
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
    selectShop(id) {
      fetch(`/api/shop/moderation/details.php?id=${id}`)
        .then(res => res.json())
        .then(json => {
          this.selected.type = "shop";
          this.selected.data = json;
        });
    },
    selectProduct(id) {
      fetch(`/api/product/moderation/details.php?id=${id}`)
        .then(res => res.json())
        .then(json => {
          this.selected.type = "product";
          this.selected.data = json;
        });
    },
    selectComment(id) {
      fetch(`/api/comment/moderation/details.php?id=${id}`)
        .then(res => res.json())
        .then(json => {
          this.selected.type = "comment";
          this.selected.data = json;
        });
    },
  },
  mounted() {
    this.getPendingShops();
    this.getPendingProducts();
    this.getPendingComments();
  },
  components: { VCol, VRow, VTabs, VTab, VTabsItems, VTabItem, VDataIterator, VCard, CommentPreview, ProductPreview, ShopPreview, ShopDetails, ProductDetails, CommentDetails }
}
</script>