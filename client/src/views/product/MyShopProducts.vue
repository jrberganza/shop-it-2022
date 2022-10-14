<template>
  <div class="my-shop-products">
    <h1>My products</h1>
    <VBtn class="my-2" @click="() => $router.push('/my/shop')">Go back</VBtn>
    <VRow>
      <VCol cols="12" lg="6" order="1" order-lg="12">
        <template v-if="myProduct">
          <VCard>
            <VImg
              :src="myProduct.photos.length > 0 ? '/api/photo/get.php?id=' + myProduct.photos[0] : '/images/placeholder.png'"
              height="250" />
            <VCardTitle>
              <VTextField label="Name" v-model="myProduct.name" :rules="[rules.required]" maxlength="255">
              </VTextField>
            </VCardTitle>
            <VCardSubtitle>
              <VTextField label="Price" v-model="myProduct.price" :rules="[rules.required]"></VTextField>
            </VCardSubtitle>
            <VCardText>
              <VTextarea label="Description" v-model="myProduct.description" :rules="[rules.required]" counter="512"
                maxlength="512"></VTextarea>
              <VCheckbox label="Disabled?" v-model="myProduct.disabled"></VCheckbox>
            </VCardText>
            <VCardActions>
              <VBtn block @click="saveProduct">
                <VIcon>mdi-floppy</VIcon> Save
              </VBtn>
            </VCardActions>
          </VCard>
        </template>
        <p v-else><em>No product selected.</em></p>
      </VCol>
      <VCol cols="12" lg="6" order="12" order-lg="1">
        <VBtn block @click="newProduct">
          <VIcon>mdi-plus</VIcon> New product
        </VBtn>
        <VDataIterator :items="products" :itemsPerPage="5">
          <template v-slot:default="{ items }">
            <VCard v-for="product in items" :key="product.id" class="my-2" @click="getProduct(product.id)">
              <VImg v-if="product.photos.length > 0" :src="'/api/photo/get.php?id=' + product.photos[0]" height="100" />
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
    /** @type {any | null} */ myProduct: null,
    products: [],
    rules: {
      required: v => !!v || "Required",
    },
  }),
  methods: {
    newProduct() {
      this.myProduct = {
        id: null,
        name: '',
        price: 0.0,
        description: '',
        disabled: true,
        photos: [],
      }
    },
    saveProduct() {
      let body = {
        id: this.myProduct.id,
        name: this.myProduct.name,
        price: parseFloat(this.myProduct.price),
        description: this.myProduct.description,
        disabled: this.myProduct.disabled,
      };
      fetch('/api/product/user/save.php', {
        method: "POST",
        body: JSON.stringify(body),
      })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.myProduct.id = json.id;
            this.getProducts();
          }
        });
    },
    getProducts() {
      fetch(`/api/product/user/all.php`)
        .then(res => res.json())
        .then(json => this.products = json.products);
    },
    getProduct(id) {
      fetch(`/api/product/user/get.php?id=${id}`)
        .then(res => res.json())
        .then(json => this.myProduct = json);
    },
  },
  mounted() {
    this.getProducts();
  },
  components: { VRow, VCol, VForm, VTextField, VTextarea, VBtn, VCheckbox, VDataIterator, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VImg, VIcon },
};
</script>