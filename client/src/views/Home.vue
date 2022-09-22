<template>
  <div class="home">
    <template v-for="feed in feeds">
      <div class="feed">
        <h1>{{feed.name}}</h1>
        <ProductList v-if="feed.type == 'product'" :products="feed.content"></ProductList>
        <ShopList v-else-if="feed.type == 'shop'" :shops="feed.content"></ShopList>
      </div>
    </template>
  </div>
</template>

<script>
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
  components: { ProductList, ShopList },
};
</script>
