<template>
  <div class="your-shops">
    <h1>Your products on shop {{$route.params.shopId}}</h1>
    <VRow>
      <VCol cols="12" lg="6" order="1" order-lg="12">
        <template v-if="selectedProduct">
          <VCard>
            <VCardTitle>
              <VTextField label="Name" v-model="selectedProduct.name"></VTextField>
            </VCardTitle>
            <VCardSubtitle>
              <VTextField label="Price" v-model="selectedProduct.price"></VTextField>
            </VCardSubtitle>
            <VCardText>
              <VTextarea label="Description" v-model="selectedProduct.desc"></VTextarea>
            </VCardText>
            <VCardActions>
              <VBtn block @click="() => selectedProduct = null">
                <VIcon>mdi-floppy</VIcon> Save
              </VBtn>
            </VCardActions>
          </VCard>
        </template>
        <p v-else><em>No product selected.</em></p>
      </VCol>
      <VCol cols="12" lg="6" order="12" order-lg="1">
        <VBtn block>
          <VIcon>mdi-plus</VIcon> New product
        </VBtn>
        <VDataIterator :items="products" :itemsPerPage="5">
          <template v-slot:default="{ items }">
            <VCard v-for="product in items" :key="product.id" class="my-2" @click="getProduct(product.id)">
              <VCardTitle>{{product.name}}</VCardTitle>
              <VCardSubtitle>{{product.price}} - {{product.shopName}}</VCardSubtitle>
              <VCardText>{{product.shortDesc}}</VCardText>
            </VCard>
          </template>
        </VDataIterator>
      </VCol>
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol, VForm, VTextField, VTextarea, VBtn, VDataIterator, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VIcon } from 'vuetify/lib';

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
  components: { VRow, VCol, VForm, VTextField, VTextarea, VBtn, VDataIterator, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VIcon },
};
</script>