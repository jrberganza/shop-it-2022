<template>
  <div class="search">
    <VRow>
      <VCol cols="12" lg="6" order-lg="1">
        <h1>Products</h1>
      </VCol>
      <VCol cols="12" sm="4" lg="6" order-lg="3">
        <VSelect v-model="selectedProductCategories" :items="productCategories" itemText="name" itemValue="id"
          label="Categories" multiple chips></VSelect>
      </VCol>
      <VCol cols="12" sm="8" lg="6" order-lg="5">
        <ProductList :products="searchResults.products"></ProductList>
      </VCol>
      <VCol cols="12" lg="6" order-lg="2">
        <h1>Shops</h1>
      </VCol>
      <VCol cols="12" sm="4" lg="6" order-lg="4">
        <VSelect v-model="selectedShopCategories" :items="shopCategories" itemText="name" itemValue="id"
          label="Categories" multiple chips></VSelect>
      </VCol>
      <VCol cols="12" sm="8" lg="6" order-lg="6">
        <ShopList :shops="searchResults.shops"></ShopList>
      </VCol>
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol } from 'vuetify/lib';
import { mapMutations, mapState } from 'vuex';
import ProductList from '../components/ProductList.vue';
import ShopList from '../components/ShopList.vue';

export default {
  name: 'Search',
  data: () => ({
    /** @type {any[]} */ searchResults: [],
    productCategories: [],
    shopCategories: [],
    selectedShopCategories: [],
    selectedProductCategories: [],
  }),
  computed: {
    ...mapState(['searchRequest']),
  },
  watch: {
    searchRequest(newVal) {
      this.$router.push(`/search/?q=${encodeURIComponent(newVal.query)}&shopCategories=${newVal.shopCategories.map(d => encodeURIComponent(d)).join(",")}&productCategories=${newVal.productCategories.map(d => encodeURIComponent(d)).join(",")}`);
      this.selectedShopCategories = newVal.shopCategories;
      this.selectedProductCategories = newVal.productCategories;

      this.getSearchResults();
    },
    selectedShopCategories(newVal) {
      this.updateSearchRequest({ ...this.searchRequest, shopCategories: newVal });
    },
    selectedProductCategories(newVal) {
      this.updateSearchRequest({ ...this.searchRequest, productCategories: newVal });
    }
  },
  methods: {
    getSearchResults() {
      fetch(`/api/search.php?q=${this.searchRequest.query}&shopCategories=${this.searchRequest.shopCategories.map(d => encodeURIComponent(d)).join(",")}&productCategories=${this.searchRequest.productCategories.map(d => encodeURIComponent(d)).join(",")}`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.searchResults = json;
          }
        });
    },
    getProductCategories() {
      fetch(`/api/category/product/all.php`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.productCategories = json.categories;
          }
        });
    },
    getShopCategories() {
      fetch(`/api/category/shop/all.php`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.shopCategories = json.categories;
          }
        });
    },
    ...mapMutations(['updateSearchRequest'])
  },
  mounted() {
    this.selectedShopCategories = this.searchRequest.shopCategories;
    this.selectedProductCategories = this.searchRequest.productCategories;

    this.getProductCategories();
    this.getShopCategories();
    this.getSearchResults();
  },
  components: { VRow, VCol, ProductList, ShopList },
};
</script>
