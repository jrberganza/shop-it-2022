<template>
  <div class="search">
    <p>Searched for: {{$route.query.q}}</p>
    <VRow>
      <VCol cols="12" md="6">
        <h1>Products</h1>
        <ProductList :products="[]"></ProductList>
      </VCol>
      <VCol cols="12" md="6">
        <h1>Shops</h1>
        <ProductList :products="[]"></ProductList> <!-- TODO: Change to ShopList -->
      </VCol>
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol } from 'vuetify/lib';
import ProductList from '../components/ProductList.vue';

export default {
  name: 'Search',
  components: { VRow, VCol, ProductList },
};
</script>
