<template>
  <div class="search">
    <VRow>
      <template v-for="feed in searchResults">
        <VCol cols="12" lg="6" class="results">
          <h1>{{feed.name}}</h1>
          <ProductList v-if="feed.type == 'product'" :products="feed.content"></ProductList>
          <ShopList v-else-if="feed.type == 'shop'" :shops="feed.content"></ShopList>
        </VCol>
      </template>
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol } from 'vuetify/lib';
import ProductList from '../components/ProductList.vue';
import ShopList from '../components/ShopList.vue';

export default {
  name: 'Search',
  data: () => ({
    /** @type {any[]} */ searchResults: [],
  }),
  methods: {
    getSearchResults() {
      fetch(`/api/search.php?q=${this.$route.query.q}`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.searchResults = json.results;
          }
        });
    },
  },
  mounted() {
    this.getSearchResults();
  },
  components: { VRow, VCol, ProductList, ShopList },
};
</script>
