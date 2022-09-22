<template>
  <div class="home">
    <VRow>
      <template v-for="feed in feeds">
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
  name: 'Home',
  data: () => ({
    /** @type {any[]} */ feeds: [],
  }),
  methods: {
    getFeeds() {
      fetch('/api/feeds.php')
        .then(res => res.json())
        .then(json => this.feeds = json);
    }
  },
  mounted() {
    this.getFeeds();
  },
  components: { VRow, VCol, ProductList, ShopList },
};
</script>
