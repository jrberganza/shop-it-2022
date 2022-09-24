<template>
  <div class="home">
    <VRow>
      <template v-for="feed in feeds">
        <VCol cols="12" lg="6" class="results">
          <h1>{{feed.name}}</h1>
          <ProductFeed v-if="feed.type == 'product'" :products="feed.content"></ProductFeed>
          <ShopFeed v-else-if="feed.type == 'shop'" :shops="feed.content"></ShopFeed>
        </VCol>
      </template>
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol } from 'vuetify/lib';
import ProductFeed from '../components/feeds/ProductFeed.vue';
import ShopFeed from '../components/feeds/ShopFeed.vue';

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
  components: { VRow, VCol, ProductFeed, ShopFeed },
};
</script>
