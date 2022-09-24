<template>
  <div class="categories">
    <VRow>
      <VCol cols="12" md="6">
        <h1>Shop Categories</h1>
        <CategoryEditor :categories="shopCategories"></CategoryEditor>
      </VCol>
      <VCol cols="12" md="6">
        <h1>Product Categories</h1>
        <CategoryEditor :categories="productCategories"></CategoryEditor>
      </VCol>
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol } from 'vuetify/lib';
import CategoryEditor from '../../components/category/CategoryEditor.vue';

export default {
  name: 'Categories',
  data: () => ({
    /** @type {any[]} */ shopCategories: [],
    /** @type {any[]} */ productCategories: [],
  }),
  methods: {
    getProductCategories() {
      fetch(`/api/product/category/all.php`)
        .then(res => res.json())
        .then(json => this.shopCategories = json);
    },
    getShopCategories() {
      fetch(`/api/shop/category/all.php`)
        .then(res => res.json())
        .then(json => this.productCategories = json);
    },
  },
  mounted() {
    this.getProductCategories();
    this.getShopCategories();
  },
  components: { VRow, VCol, CategoryEditor },
};
</script>