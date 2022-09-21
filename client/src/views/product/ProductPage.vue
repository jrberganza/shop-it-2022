<template>
  <div class="product">
    <template v-if="product">
      <h1>{{product.name}}</h1>
      <p>{{product.price}} - {{product.shopName}}</p>
      <p>{{product.shortDesc}}</p>
    </template>
    <template v-else>
      <VSkeletonLoader type="card"></VSkeletonLoader>
    </template>
  </div>
</template>

<script>

export default {
  name: 'ProductPage',
  props: ['id'],
  data: () => ({
    /** @type {any | null} */ product: null,
  }),
  methods: {
    getProduct(id) {
      let apiResponse = [
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
      ]; // example

      return apiResponse[id];
    }
  },
  mounted() {
    this.product = this.getProduct(this.$route.params.id);
  },
  components: {},
};
</script>
