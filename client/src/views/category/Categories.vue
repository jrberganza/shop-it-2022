<template>
  <div class="categories">
    <h1>Categories</h1>
    <VTabs v-model="tab">
      <VTabsSlider color="accent"></VTabsSlider>
      <VTab>
        Shop
      </VTab>
      <VTab>
        Product
      </VTab>
    </VTabs>
    <VTabsItems v-model="tab">
      <VTabItem>
        <CategoryEditor :categories="shopCategories"></CategoryEditor>
      </VTabItem>
      <VTabItem>
        <CategoryEditor :categories="productCategories"></CategoryEditor>
      </VTabItem>
    </VTabsItems>
  </div>
</template>

<script>
import { VRow, VCol, VTabs, VTab, VTabsItems, VTabItem, VTabsSlider } from 'vuetify/lib';
import CategoryEditor from '../../components/category/CategoryEditor.vue';

export default {
  name: 'Categories',
  data: () => ({
    tab: null,
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
  components: { VRow, VCol, VTabs, VTab, VTabsItems, VTabItem, VTabsSlider, CategoryEditor },
};
</script>