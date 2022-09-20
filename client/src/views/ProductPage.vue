<template>
  <div class="product">
    <p>Producto {{$route.params.id}}</p>
  </div>
</template>

<script>

export default {
  name: 'ProductPage',
  props: ['id'],
  data: () => ({}),
  components: {},
};
</script>
