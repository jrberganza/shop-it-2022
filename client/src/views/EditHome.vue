<template>
  <div class="home">
    <VBtn block class="mb-5" @click="saveHomepage">
      <VIcon>mdi-floppy</VIcon> Save
    </VBtn>
    <VRow>
      <template v-for="block, i in blocks">
        <VCol cols="12" :lg="{ full: 12, half: 6, third: 4, fourth: 3, twelfth: 1 }[block.size]" class="results">
          <VCard class="pa-4">
            <VRow>
              <VCol cols="6">
                <VBtn block text @click="() => insertBefore(i)">Insert before</VBtn>
              </VCol>
              <VCol cols="6">
                <VBtn block text @click="() => insertAfter(i)">Insert after</VBtn>
              </VCol>
            </VRow>
            <VSelect v-model="block.blockType" :items="blockTypes" itemText="text" itemValue="value" label="Type">
            </VSelect>
            <VSelect v-model="block.size" :items="blockSizes" itemText="text" itemValue="value" label="Size"></VSelect>
            <template v-if="block.blockType == 'feed'">
              <VTextField v-model="block.feedTitle" label="Feed title"></VTextField>
              <VSelect v-model="block.feedType" :items="feedTypes" itemText="text" itemValue="value" label="Feed type">
              </VSelect>
              <VSelect v-model="block.feedItemType" :items="feedItemTypes" itemText="text" itemValue="value"
                label="Item type"></VSelect>
              <VTextField v-model="block.feedMaxSize" label="Max. feed items"></VTextField>
            </template>
            <template v-else-if="block.blockType == 'banner'">
              <PhotoInput v-model="block.bannerPhotoId" />
              <VTextField v-model="block.bannerTitle" label="Banner title"></VTextField>
              <VTextField v-model="block.bannerText" label="Banner text"></VTextField>
            </template>
            <VBtn block text @click="() => deleteBlock(i)" :disabled="blocks.length == 1">Delete</VBtn>
          </VCard>
        </VCol>
      </template>
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol } from 'vuetify/lib';
import PhotoInput from '../components/photo/PhotoInput.vue';

export default {
  name: 'Home',
  data: () => ({
    /** @type {any[]} */ blocks: [],
    blockTypes: [
      { text: "Feed", value: "feed" },
      { text: "Banner", value: "banner" },
    ],
    blockSizes: [
      { text: "Full", value: "full" },
      { text: "Half", value: "half" },
      { text: "Third", value: "third" },
      { text: "Fourth", value: "fourth" },
      { text: "Twelfth", value: "twelfth" },
    ],
    feedTypes: [
      { text: "Top rated", value: "auto_top_rated" },
      { text: "Trending", value: "auto_trending" },
      { text: "Recent", value: "auto_recent" },
      { text: "Manual", value: "manual" },
    ],
    feedItemTypes: [
      { text: "Shops", value: "shop" },
      { text: "Products", value: "product" },
    ],
  }),
  methods: {
    getHomepage() {
      fetch('/api/homepage/admin/get.php')
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.blocks = json.blocks
          }
        });
    },
    saveHomepage() {
      fetch('/api/homepage/admin/save.php', {
        method: "POST",
        body: JSON.stringify({ blocks: this.blocks }),
      })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.getHomepage();
          }
        });
    },
    newBlock() {
      return {
        blockType: "feed",
        size: "full",
        feedTitle: "",
        feedType: "manual",
        feedItemType: "product",
        feedMaxSize: 5,
        bannerTitle: "",
        bannerText: "",
        bannerPhotoId: null,
      }
    },
    insertBefore(i) {
      this.blocks.splice(i, 0, this.newBlock());
    },
    insertAfter(i) {
      this.blocks.splice(i + 1, 0, this.newBlock());
    },
    deleteBlock(i) {
      if (this.blocks.length == 1) {
        this.blocks.splice(i, 1, this.newBlock());
      } else {
        this.blocks.splice(i, 1);
      }
    },
  },
  mounted() {
    this.getHomepage();
  },
  components: { VRow, VCol, PhotoInput },
};
</script>
