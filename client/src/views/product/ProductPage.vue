<template>
  <div class="product">
    <template v-if="product">
      <h1>{{product.name}}</h1>
      <p>{{product.price}} - {{product.shopName}}</p>
      <p>{{product.shortDesc}}</p>

      <CommentTree :comments="comments"></CommentTree>
    </template>
    <template v-else>
      <VSkeletonLoader type="card"></VSkeletonLoader>
    </template>
  </div>
</template>

<script>
import CommentTree from '../../components/comments/CommentTree.vue';

export default {
  name: 'ProductPage',
  data: () => ({
    /** @type {any | null} */ product: null,
    comments: [
      {
        data: {
          author: 'Jake Jake',
          publishedAt: "31/12/2022",
          content: 'Hey!',
        },
        children: [
          {
            data: {
              author: 'Jake Jake',
              publishedAt: "31/12/2022",
              content: 'Hey!',
            },
            children: [
              {
                data: {
                  author: 'Jake Jake',
                  publishedAt: "31/12/2022",
                  content: 'Hey!',
                },
              },
              {
                data: {
                  author: 'Jake Jake',
                  publishedAt: "31/12/2022",
                  content: 'Hey!',
                },
              },
            ]
          },
          {
            data: {
              author: 'Jake Jake',
              publishedAt: "31/12/2022",
              content: 'Hey!',
            },
          },
        ]
      },
      {
        data: {
          author: 'Jake Jake',
          publishedAt: "31/12/2022",
          content: 'Hey!',
        },
        children: [
          {
            data: {
              author: 'Jake Jake',
              publishedAt: "31/12/2022",
              content: 'Hey!',
            },
          },
          {
            data: {
              author: 'Jake Jake',
              publishedAt: "31/12/2022",
              content: 'Hey!',
            },
          },
        ]
      },
    ]
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
  components: { CommentTree },
};
</script>
