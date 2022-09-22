<template>
  <div class="your-shops">
    <h1>Your shops</h1>
    <VRow>
      <VCol cols="12" lg="4">
        <VVirtualScroll :items="shops" :itemHeight="320" height="500" bench="1">
          <template v-slot:default="{ item }">
            <VCard class="mx-2" height="300" @click="selectShop(item.id)">
              <img src="@/assets/logo.png" width="100%" height="100" /> <!-- TODO: Change to map -->
              <VCardTitle>{{item.name}}</VCardTitle>
              <VCardSubtitle>{{item.address}} - {{item.phoneNumber}}</VCardSubtitle>
              <VCardText>{{item.shortDesc}}</VCardText>
            </VCard>
          </template>
        </VVirtualScroll>
        <VBtn block>
          <VIcon>mdi-plus</VIcon> New shop
        </VBtn>
      </VCol>
      <VCol cols="12" lg="8">
        <template v-if="selectedShop">
          <VForm>
            <VTextField label="Name" v-model="selectedShop.name"></VTextField>
            <VTextField label="Address" v-model="selectedShop.address"></VTextField>
            <VTextField label="Phone Number" v-model="selectedShop.phoneNumber"></VTextField>
            <VTextField label="Description" v-model="selectedShop.desc"></VTextField>
            <VBtn block @click="() => selectedShop = null">
              <VIcon>mdi-floppy</VIcon> Save
            </VBtn>
          </VForm>
        </template>
        <template v-else><em>No shop selected.</em></template>
      </VCol>
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol, VForm, VTextField, VBtn, VVirtualScroll, VCard, VCardTitle, VCardSubtitle, VCardText, VIcon } from 'vuetify/lib';

export default {
  name: 'YourShops',
  data: () => ({
    /** @type {any | null} */ selectedShop: null,
    shops: []
  }),
  methods: {
    getShops() {
      fetch('http://localhost/api/shop/user/all.php')
        .then(res => res.json())
        .then(json => this.shops = json);
    },
    getShop(id) {
      fetch(`http://localhost/api/shop/get.php?id=${id}`)
        .then(res => res.json())
        .then(json => this.selectedShop = json);
    },
    selectShop(id) {
      this.selectedShop = this.getShop(id);
    },
  },
  mounted() {
    this.getShops();
  },
  components: { VRow, VCol, VForm, VTextField, VBtn, VVirtualScroll, VCard, VCardTitle, VCardSubtitle, VCardText, VIcon },
};
</script>