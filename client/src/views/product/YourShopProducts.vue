<template>
  <div class="your-shops">
    <h1>Your products on shop {{$route.params.shopId}}</h1>
    <VRow>
      <VCol cols="12" lg="4">
        <VVirtualScroll :items="products" :itemHeight="320" height="500" bench="1">
          <template v-slot:default="{ item }">
            <VCard class="mx-2" height="300" @click="getProduct(item.id)">
              <VCardTitle>{{item.name}}</VCardTitle>
              <VCardSubtitle>{{item.price}} - {{item.shopName}}</VCardSubtitle>
              <VCardText>{{item.shortDesc}}</VCardText>
            </VCard>
          </template>
        </VVirtualScroll>
        <VBtn block>
          <VIcon>mdi-plus</VIcon> New product
        </VBtn>
      </VCol>
      <VCol cols="12" lg="8">
        <template v-if="selectedProduct">
          <VForm>
            <VTextField label="Name" v-model="selectedProduct.name"></VTextField>
            <VTextField label="Price" v-model="selectedProduct.price"></VTextField>
            <VTextField label="Description" v-model="selectedProduct.desc"></VTextField>
            <VBtn block @click="() => selectedProduct = null">
              <VIcon>mdi-floppy</VIcon> Save
            </VBtn>
          </VForm>
        </template>
        <template v-else><em>No product selected.</em></template>
      </VCol>
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol, VForm, VTextField, VBtn, VVirtualScroll, VCard, VCardTitle, VCardSubtitle, VCardText, VIcon } from 'vuetify/lib';

export default {
  name: 'YourShopProducts',
  data: () => ({
    /** @type {any | null} */ selectedProduct: null,
    products: []
  }),
  methods: {
    getProducts(shopId) {
      fetch(`/api/product/shop/all.php?shopId=${shopId}`)
        .then(res => res.json())
        .then(json => this.products = json);
    },
    getProduct(id) {
      fetch(`/api/product/get.php?id=${id}`)
        .then(res => res.json())
        .then(json => this.selectedProduct = json);
    },
  },
  mounted() {
    this.getProducts(this.$route.params.shopId);
  },
  components: { VRow, VCol, VForm, VTextField, VBtn, VVirtualScroll, VCard, VCardTitle, VCardSubtitle, VCardText, VIcon },
};
</script>