<template>
  <div class="home">
    <VRow>
      <template v-for="block in blocks">
        <VCol cols="12" :lg="{ full: 12, half: 6, third: 4, fourth: 3, twelfth: 1 }[block.size]" class="results">
          <template v-if="block.blockType == 'feed'">
            <h1>{{block.feedTitle}}</h1>
            <ProductFeed v-if="block.feedItemType == 'product'" :products="block.feedContent || []"></ProductFeed>
            <ShopFeed v-else-if="block.feedItemType == 'shop'" :shops="block.feedContent || []"></ShopFeed>
          </template>
          <template v-else-if="block.blockType == 'banner'">
            <VCard>
              <VImg v-if="block.bannerPhotoId" :src="'/api/photo/get.php?id=' + block.bannerPhotoId" height="350">
              </VImg>
              <VCardTitle v-if="block.bannerTitle">{{block.bannerTitle}}</VCardTitle>
              <VCardText v-if="block.bannerText">{{block.bannerText}}</VCardText>
            </VCard>
          </template>
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
    /** @type {any[]} */ blocks: [],
  }),
  methods: {
    getFeeds() {
      fetch('/api/homepage/get.php')
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.blocks = json.blocks
          }
        });
    }
  },
  mounted() {
    this.getFeeds();
  },
  components: { VRow, VCol, ProductFeed, ShopFeed },
};
</script>
