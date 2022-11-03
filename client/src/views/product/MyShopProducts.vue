<template>
  <div class="my-shop-products">
    <h1>My products</h1>
    <VBtn class="my-2" @click="() => $router.push('/my/shop')">Go back</VBtn>
    <VRow>
      <VCol cols="12" lg="6" order="1" order-lg="12">
        <template v-if="myProduct">
          <VCard>
            <VCardTitle>
              <VTextField label="Name" v-model="myProduct.name" :rules="[rules.required]" maxlength="255">
              </VTextField>
            </VCardTitle>
            <VCardSubtitle>
              <PhotoInput v-model="myProduct.photos" multiple />
              <VTextField label="Price" v-model="myProduct.price" :rules="[rules.required]"></VTextField>
            </VCardSubtitle>
            <VCardText>
              <VTextarea label="Description" v-model="myProduct.description" :rules="[rules.required]" counter="512"
                maxlength="512"></VTextarea>
              <VSelect v-model="myProduct.categories" :items="productCategories" itemText="name" itemValue="id"
                label="Categories" multiple chips></VSelect>
              <VCheckbox label="Disabled?" v-model="myProduct.disabled"></VCheckbox>
            </VCardText>
            <VCardText v-if="myProduct.id && !myProduct.moderated">
              <em>This product hasn't been checked by a moderator</em>
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
              <VCardTitle>{{ product.name }}</VCardTitle>
              <VCardSubtitle>{{ product.price }} - {{ product.shopName }}</VCardSubtitle>
              <VCardText>{{ product.description }}</VCardText>
              <VCardText v-if="product.id && !product.moderated">
                <em>This product hasn't been checked by a moderator</em>
              </VCardText>
            </VCard>
          </template>
        </VDataIterator>
      </VCol>
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol, VForm, VTextField, VTextarea, VBtn, VCheckbox, VDataIterator, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VImg, VIcon } from 'vuetify/lib';
import PhotoInput from '../../components/photo/PhotoInput.vue';
import { mapMutations } from 'vuex';

export default {
  name: 'MyShopProducts',
  data: () => ({
    /** @type {any | null} */ myProduct: null,
    productCategories: [],
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
        categories: [],
        disabled: true,
        photos: [],
        moderated: false,
      }
    },
    saveProduct() {
      let body = {
        id: this.myProduct.id,
        name: this.myProduct.name,
        price: parseFloat(this.myProduct.price),
        description: this.myProduct.description,
        categories: this.myProduct.categories,
        disabled: this.myProduct.disabled,
        photos: this.myProduct.photos,
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
          } else {
            this.openSnackbar({ shown: true, message: json._error });
          }
        });
    },
    getProducts() {
      fetch(`/api/product/user/all.php`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.products = json.products
          } else {
            this.openSnackbar({ shown: true, message: json._error });
          }
        });
    },
    getProduct(id) {
      fetch(`/api/product/user/get.php?id=${id}`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.myProduct = json
          } else {
            this.openSnackbar({ shown: true, message: json._error });
          }
        });
    },
    getProductCategories() {
      fetch(`/api/category/product/all.php`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.productCategories = json.categories;
          } else {
            this.openSnackbar({ shown: true, message: json._error });
          }
        });
    },
    ...mapMutations(['openSnackbar']),
  },
  mounted() {
    this.getProducts();
    this.getProductCategories();
  },
  components: { VRow, VCol, VForm, VTextField, VTextarea, VBtn, VCheckbox, VDataIterator, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VImg, VIcon, PhotoInput },
};
</script>