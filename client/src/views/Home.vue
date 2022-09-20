<template>
  <div class="home">
    <h1>For you</h1>
    <ProductList :products="products.forYou"></ProductList>
    <h1>Trending</h1>
    <ProductList :products="products.trending"></ProductList>
    <h1>Recent</h1>
    <ProductList :products="products.recent"></ProductList>
  </div>
</template>

<script>
import ProductList from '../components/ProductList.vue';

export default {
  name: 'Home',
  data: () => ({
    products: {
      forYou: [
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
      trending: [
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
      recent: [
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
    },
  }),
  components: { ProductList },
};
</script>
