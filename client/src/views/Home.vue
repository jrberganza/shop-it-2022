<template>
  <div class="home">
    <ProductList :shops="shops"></ProductList>
  </div>
</template>

<script>
import ProductList from '../components/ProductList.vue';

export default {
  name: 'Home',
  data: () => ({
    shops: [
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
  }),
  components: { ProductList },
};
</script>
