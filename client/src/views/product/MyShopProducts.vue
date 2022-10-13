<template>
  <div class="my-shop-products">
    <h1>My products</h1>
    <VBtn class="my-2" @click="() => $router.push('/my/shop')">Go back</VBtn>
    <VRow>
      <VCol cols="12" lg="6" order="1" order-lg="12">
        <template v-if="selectedProduct">
          <VCard>
            <VImg v-if="selectedProduct.photos.length > 0"
              :src="'/api/shop/product/get.php?id=' + selectedProduct.photos[0]" height="250" />
            <VImg v-else src="/images/placeholder.png" height="250" />
            <VCardTitle>
              <VTextField label="Name" v-model="selectedProduct.name" :rules="[rules.required]" maxlength="255">
              </VTextField>
            </VCardTitle>
            <VCardSubtitle>
              <VTextField label="Price" v-model="selectedProduct.price" :rules="[rules.required]"></VTextField>
            </VCardSubtitle>
            <VCardText>
              <VTextarea label="Description" v-model="selectedProduct.description" :rules="[rules.required]"
                counter="512" maxlength="512"></VTextarea>
              <VCheckbox label="Disabled?" v-model="selectedProduct.disabled"></VCheckbox>
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
              <VImg v-if="product.photos.length > 0" :src="'/api/shop/product/get.php?id=' + product.photos[0]"
                height="100" />
              <VImg v-else src="/images/placeholder.png" height="100" />
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
import { VRow, VCol, VForm, VTextField, VTextarea, VBtn, VCheckbox, VDataIterator, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VImg, VIcon } from 'vuetify/lib';

export default {
  name: 'MyShopProducts',
  data: () => ({
    /** @type {any | null} */ selectedProduct: null,
    products: [],
    rules: {
      required: v => !!v || "Required",
    },
  }),
  methods: {
    getProducts(shopId) {
      fetch(`/api/product/user/all.php`)
        .then(res => res.json())
        .then(json => this.products = json.products);
    },
    getProduct(id) {
      fetch(`/api/product/user/get.php?id=${id}`)
        .then(res => res.json())
        .then(json => this.selectedProduct = json);
    },
  },
  mounted() {
    this.getProducts(this.$route.params.shopId);
  },
  components: { VRow, VCol, VForm, VTextField, VTextarea, VBtn, VCheckbox, VDataIterator, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VImg, VIcon },
};
</script>