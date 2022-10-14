<template>
  <div class="categories">
    <h1>Categories</h1>
    <VRow>
      <VCol cols="12" lg="6" order="1" order-lg="12">
        <VCard>
          <VCardTitle>
            <VTextField label="Name" v-model="myCategory.name" :rules="[rules.required]">
            </VTextField>
          </VCardTitle>
          <VCardText>
            <VCheckbox label="Disabled?" v-model="myCategory.disabled"></VCheckbox>
          </VCardText>
          <VCardActions>
            <VRow v-if="myCategory.id">
              <VCol cols="6">
                <VBtn block @click="newCategory">
                  <VIcon>mdi-close</VIcon> Discard
                </VBtn>
              </VCol>
              <VCol cols="6">
                <VBtn block @click="saveCategory">
                  <VIcon>mdi-floppy</VIcon> Save
                </VBtn>
              </VCol>
            </VRow>
            <VBtn v-else block @click="saveCategory">
              <VIcon>mdi-plus</VIcon> Create
            </VBtn>
          </VCardActions>
        </VCard>
      </VCol>
      <VCol cols="12" lg="6" order="12" order-lg="1">
        <VTabs v-model="tab">
          <VTabsSlider color="accent"></VTabsSlider>
          <VTab>
            Shop
          </VTab>
          <VTab>
            Product
          </VTab>
        </VTabs>
        <VTabsItems v-model="tab">
          <VTabItem v-for="categories, i in [shopCategories, productCategories]" :key="i">
            <VDataIterator :items="categories" :itemsPerPage="5">
              <template v-slot:default="{ items }">
                <VCard v-for="item in items" class="my-4" :key="item.id" @click="() => myCategory = {...item}">
                  <VCardTitle>{{item.name}}</VCardTitle>
                  <VCardText>
                    <template v-if="item.disabled">
                      <VIcon small>mdi-close</VIcon> Disabled
                    </template>
                    <template v-else>
                      <VIcon small>mdi-check</VIcon> Enabled
                    </template>
                  </VCardText>
                </VCard>
              </template>
            </VDataIterator>
          </VTabItem>
        </VTabsItems>
      </VCol>
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol, VTabs, VTab, VTabsItems, VTabItem, VTabsSlider, VDataIterator, VTextField, VCheckbox, VBtn, VIcon, VCard, VCardTitle, VCardText, VCardActions } from 'vuetify/lib';

export default {
  name: 'Categories',
  data: () => ({
    tab: null,
    /** @type {any[]} */ shopCategories: [],
    /** @type {any[]} */ productCategories: [],
    rules: {
      required: v => !!v || "Required",
    },
    myCategory: {
      id: null,
      name: "",
      disabled: true,
      type: null,
    }
  }),
  methods: {
    newCategory() {
      this.myCategory = {
        id: null,
        name: "",
        disabled: true,
        type: null,
      };
    },
    saveCategory() {
      let body = {
        id: this.myCategory.id,
        name: this.myCategory.name,
        disabled: this.myCategory.disabled,
        type: this.myCategory.type || ["shop", "product"][this.tab],
      }
      fetch(`/api/category/admin/save.php`, {
        method: "POST",
        body: JSON.stringify(body)
      })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.getProductCategories();
            this.getShopCategories();
            this.newCategory();
          }
        });
    },
    getProductCategories() {
      fetch(`/api/category/admin/allProduct.php`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.productCategories = json.categories;
          }
        });
    },
    getShopCategories() {
      fetch(`/api/category/admin/allShop.php`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.shopCategories = json.categories;
          }
        });
    },
  },
  mounted() {
    this.getProductCategories();
    this.getShopCategories();
  },
  components: { VRow, VCol, VTabs, VTab, VTabsItems, VTabItem, VTabsSlider, VDataIterator, VTextField, VCheckbox, VBtn, VIcon, VCard, VCardTitle, VCardText, VCardActions },
};
</script>