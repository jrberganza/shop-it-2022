<template>
  <div class="search">
    <VRow>
      <VCol cols="12" lg="4">
        <VSelect v-model="selectedShopCategories" :items="shopCategories" itemText="name" itemValue="id"
          label="Shop Categories" multiple chips></VSelect>
        <VSelect v-model="selectedProductCategories" :items="productCategories" itemText="name" itemValue="id"
          label="Product Categories" multiple chips></VSelect>
        <VSelect label="Department" :disabled="departments == null" :items="departments" itemText="name" itemValue="id"
          v-model="selectedDepartment" maxlength="255">
        </VSelect>
        <VSelect label="Municipalities" :disabled="municipalities == null || selectedDepartment == null"
          :items="municipalities" itemText="name" itemValue="id" v-model="selectedMunicipality" maxlength="255">
        </VSelect>
        <VTextField label="Zone" :disabled="departments == null || municipalities == null" v-model="selectedZone"
          :rules="[rules.number.format]" maxlength="10"></VTextField>
      </VCol>
      <VCol cols="12" lg="8">
        <h1>Products</h1>
        <ProductList :products="searchResults.products"></ProductList>
        <h1>Shops</h1>
        <ShopList :shops="searchResults.shops"></ShopList>
      </VCol>
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol } from 'vuetify/lib';
import { mapMutations, mapState } from 'vuex';
import ProductList from '../components/ProductList.vue';
import ShopList from '../components/ShopList.vue';

export default {
  name: 'Search',
  data: () => ({
    /** @type {any[]} */ searchResults: [],
    productCategories: [],
    shopCategories: [],
    selectedShopCategories: [],
    selectedProductCategories: [],
    selectedDepartment: '',
    selectedMunicipality: '',
    selectedZone: '',
    departments: null,
    municipalities: null,
    rules: {
      required: v => !!v || "Required",
      number: {
        format: v => (/^\d+$/).test(v) || "Invalid zone"
      },
      phoneNumber: {
        format: v => (/^\+?\d+$/).test(v) || "Invalid phone number"
      },
    }
  }),
  computed: {
    ...mapState(['searchRequest']),
  },
  watch: {
    searchRequest(newVal) {
      this.$router.push(`/search/?q=${encodeURIComponent(newVal.query)}&shopCategories=${newVal.shopCategories.map(d => encodeURIComponent(d)).join(",")}&productCategories=${newVal.productCategories.map(d => encodeURIComponent(d)).join(",")}&department=${newVal.department}&municipality=${newVal.municipality}&zone=${newVal.zone}`);
      this.selectedShopCategories = newVal.shopCategories;
      this.selectedProductCategories = newVal.productCategories;

      this.getSearchResults();
    },
    selectedShopCategories(newVal) {
      this.updateSearchRequest({ ...this.searchRequest, shopCategories: newVal });
    },
    selectedProductCategories(newVal) {
      this.updateSearchRequest({ ...this.searchRequest, productCategories: newVal });
    },
    selectedDepartment(newVal) {
      this.getMunicipalities(newVal);
      this.updateSearchRequest({ ...this.searchRequest, department: newVal });
    },
    selectedMunicipality(newVal) {
      this.updateSearchRequest({ ...this.searchRequest, municipality: newVal });
    },
    selectedZone(newVal) {
      this.updateSearchRequest({ ...this.searchRequest, zone: newVal });
    },
  },
  methods: {
    getSearchResults() {
      fetch(`/api/search.php?q=${this.searchRequest.query}&shopCategories=${this.searchRequest.shopCategories.map(d => encodeURIComponent(d)).join(",")}&productCategories=${this.searchRequest.productCategories.map(d => encodeURIComponent(d)).join(",")}&department=${this.searchRequest.department}&municipality=${this.searchRequest.municipality}&zone=${this.searchRequest.zone}`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.searchResults = json;
          }
        });
    },
    getProductCategories() {
      fetch(`/api/category/product/all.php`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.productCategories = json.categories;
          }
        });
    },
    getShopCategories() {
      fetch(`/api/category/shop/all.php`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.shopCategories = json.categories;
          }
        });
    },
    getDepartments() {
      fetch(`/api/location/departments.php`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.departments = [{ id: '', name: '-' }, ...json.departments];
          }
        });
    },
    getMunicipalities(department) {
      this.municipalities = null;
      fetch(`/api/location/municipalities.php?department=${department}`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.municipalities = [{ id: '', name: '-' }, ...json.municipalities];
          }
        });
    },
    ...mapMutations(['updateSearchRequest'])
  },
  mounted() {
    this.selectedShopCategories = this.searchRequest.shopCategories;
    this.selectedProductCategories = this.searchRequest.productCategories;

    this.getDepartments();
    this.getProductCategories();
    this.getShopCategories();
    this.getSearchResults();
  },
  components: { VRow, VCol, ProductList, ShopList },
};
</script>
