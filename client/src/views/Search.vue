<template>
  <div class="search">
    <VRow>
      <VCol cols="12" md="6">
        <h1>Products</h1>
        <ProductList :products="products"></ProductList>
      </VCol>
      <VCol cols="12" md="6">
        <h1>Shops</h1>
        <ShopList :shops="shops"></ShopList>
      </VCol>
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol } from 'vuetify/lib';
import ProductList from '../components/ProductList.vue';
import ShopList from '../components/ShopList.vue';

export default {
  name: 'Search',
  data: () => ({
    products: [
      {
        id: 1,
        name: 'ola',
        shopName: 'tienda2',
        price: 12.34,
        shortDesc: 'example product 1',
      },
      {
        id: 2,
        name: 'ola2',
        shopName: 'tienda2',
        price: 12.34,
        shortDesc: 'example product 2',
      },
    ],
    shops: [
      {
        id: 1,
        name: 'tienda1',
        address: 'aquí',
        phoneNumber: '1234-5678',
        shortDesc: 'example shop 1',
      },
      {
        id: 2,
        name: 'tienda2',
        address: 'allá',
        phoneNumber: '5678-1234',
        shortDesc: 'example shop 2',
      },
    ],
  }),
  components: { VRow, VCol, ProductList, ShopList },
};
</script>
