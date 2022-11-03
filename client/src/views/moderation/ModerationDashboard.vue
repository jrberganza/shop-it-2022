<template>
  <div class="dashboard">
    <VRow>
      <VCol cols="12" lg="6" order="1" order-lg="12">
        <ShopDetails v-if="selected.type == 'shop'" :shop="selected.data"
          @publish="publish(selected.type, selected.data.id)" @reject="reject(selected.type, selected.data.id)">
        </ShopDetails>
        <ProductDetails v-else-if="selected.type == 'product'" :product="selected.data"
          @publish="publish(selected.type, selected.data.id)" @reject="reject(selected.type, selected.data.id)">
        </ProductDetails>
        <CommentDetails v-else-if="selected.type == 'comment'" :comment="selected.data"
          @publish="publish(selected.type, selected.data.id)" @reject="reject(selected.type, selected.data.id)">
        </CommentDetails>
        <p v-else><em>No item has been selected</em></p>
      </VCol>
      <VCol cols="12" lg="6" order="12" order-lg="1">
        <VTabs v-model="tab">
          <VTabsSlider color="accent"></VTabsSlider>
          <VTab v-for="tab in tabs" :key="tab">
            {{ tab }}
          </VTab>
        </VTabs>
        <VTabsItems v-model="tab">
          <VTabItem class="ma-2">
            <VDataIterator :items="pending.shop" :items-per-page="5">
              <template v-slot:default="{ items }">
                <ShopPreview class="my-2" v-for="shop in items" :key="shop.id" :shop="shop"
                  @seeDetails="select('shop', shop.id)" @publish="publish('shop', shop.id)"
                  @reject="reject('shop', shop.id)">
                </ShopPreview>
              </template>
            </VDataIterator>
          </VTabItem>
          <VTabItem class="ma-2">
            <VDataIterator :items="pending.product" :items-per-page="5">
              <template v-slot:default="{ items }">
                <ProductPreview class="my-2" v-for="product in items" :key="product.id" :product="product"
                  @seeDetails="select('product', product.id)" @publish="publish('product', product.id)"
                  @reject="reject('product', product.id)"></ProductPreview>
              </template>
            </VDataIterator>
          </VTabItem>
          <VTabItem class="ma-2">
            <VDataIterator :items="pending.comment" :items-per-page="5">
              <template v-slot:default="{ items }">
                <CommentPreview class="my-2" v-for="comment in items" :key="comment.id" :comment="comment"
                  @seeDetails="select('comment', comment.id)" @publish="publish('comment', comment.id)"
                  @reject="reject('comment', comment.id)"></CommentPreview>
              </template>
            </VDataIterator>
          </VTabItem>
        </VTabsItems>
      </VCol>
    </VRow>
  </div>
</template>

<script>
import { VCol, VRow, VTabs, VTab, VTabsItems, VTabItem, VDataIterator, VCard, VTabsSlider } from 'vuetify/lib';
import ShopPreview from '../../components/moderation/preview/ShopPreview.vue';
import ProductPreview from '../../components/moderation/preview/ProductPreview.vue';
import CommentPreview from '../../components/moderation/preview/CommentPreview.vue';
import ShopDetails from '../../components/moderation/details/ShopDetails.vue';
import ProductDetails from '../../components/moderation/details/ProductDetails.vue';
import CommentDetails from '../../components/moderation/details/CommentDetails.vue';

export default {
  name: 'Dashboard',
  data: () => ({
    selected: {
      /** @type {any | null} */ type: null,
      data: {}
    },
    tabs: ['Shops', 'Products', 'Comments'],
    tab: null,
    pending: {
      /** @type {any[]} */ shop: [],
      /** @type {any[]} */ product: [],
      /** @type {any[]} */ comment: [],
    },
  }),
  methods: {
    getPending(/** @type {"shop"|"product"|"comment"} */ type) {
      fetch(`/api/moderation/${type}/pending.php`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.pending[type] = json.pending;
          }
        });
    },
    select(/** @type {"shop"|"product"|"comment"} */ type, id) {
      fetch(`/api/moderation/${type}/details.php?id=${id}`)
        .then(res => res.json())
        .then(json => {
          this.selected.type = type;
          this.selected.data = json;
        });
    },
    publish(/** @type {"shop"|"product"|"comment"} */ type, id) {
      fetch(`/api/moderation/${type}/publish.php`, {
        "method": "POST",
        "body": JSON.stringify({ id })
      })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.getPending(type);
            if (this.selected.data.id == id) {
              this.selected.type = null;
            }
          }
        });
    },
    reject(/** @type {"shop"|"product"|"comment"} */ type, id) {
      fetch(`/api/moderation/${type}/reject.php`, {
        "method": "POST",
        "body": JSON.stringify({ id })
      })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.getPending(type);
            if (this.selected.data.id == id) {
              this.selected.type = null;
            }
          }
        });
    },
  },
  mounted() {
    this.getPending("shop");
    this.getPending("product");
    this.getPending("comment");
  },
  components: { VCol, VRow, VTabs, VTab, VTabsItems, VTabItem, VDataIterator, VCard, VTabsSlider, CommentPreview, ProductPreview, ShopPreview, ShopDetails, ProductDetails, CommentDetails }
}
</script>