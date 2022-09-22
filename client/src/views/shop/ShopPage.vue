<template>
  <div class="shop">
    <template v-if="shop">
      <VRow>
        <VCol cols="12" md="8">
          <h1>{{shop.name}}</h1>
          <p>{{shop.address}} - {{shop.phoneNumber}}</p>
          <p>{{shop.shortDesc}}</p>
        </VCol>
        <VCol cols="12" md="4">
          <img src="@/assets/logo.png" width="100%" height="100%" /> <!-- TODO: Change to map -->
        </VCol>
      </VRow>
      <CommentTree :comments="comments"></CommentTree>
    </template>
    <template v-else>
      <VSkeletonLoader type="card"></VSkeletonLoader>
    </template>
  </div>
</template>

<script>
import { VRow, VCol, VSkeletonLoader } from 'vuetify/lib';
import CommentTree from '../../components/comments/CommentTree.vue';

export default {
  name: 'ShopPage',
  data: () => ({
    /** @type {any | null} */ shop: null,
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
    getShop(id) {
      let apiResponse = [
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
      ]; // example

      return apiResponse[id];
    }
  },
  mounted() {
    this.shop = this.getShop(this.$route.params.id);
  },
  components: { VRow, VCol, VSkeletonLoader, CommentTree },
};
</script>