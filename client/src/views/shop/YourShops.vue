<template>
  <div class="your-shops">
    <h1>Your shops</h1>
    <VRow>
      <VCol cols="12" lg="6" order="1" order-lg="12">
        <template v-if="selectedShop">
          <VCard>
            <VImg src="/images/placeholder.png" height="300" />
            <VCardTitle>
              <VTextField label="Name" v-model="selectedShop.name"></VTextField>
            </VCardTitle>
            <VCardSubtitle>
              <VTextField label="Address" v-model="selectedShop.address"></VTextField>
              <VTextField label="Phone Number" v-model="selectedShop.phoneNumber"></VTextField>
            </VCardSubtitle>
            <VCardText>
              <VTextarea label="Description" v-model="selectedShop.description"></VTextarea>
              <VCheckbox label="Disabled?" v-model="selectedShop.disabled"></VCheckbox>
            </VCardText>
            <VCardActions>
              <VRow>
                <VCol cols="12" sm="4">
                  <VBtn block @click="saveShop">
                    <VIcon>mdi-floppy</VIcon> Save
                  </VBtn>
                </VCol>
                <VCol cols="12" sm="4">
                  <VBtn block :disabled="!selectedShop.id"
                    @click="() => $router.push(`/your/shop/${selectedShop.id}/products`)">
                    <VIcon>mdi-shopping</VIcon> Products
                  </VBtn>
                </VCol>
                <VCol cols="12" sm="4">
                  <VBtn block :disabled="!selectedShop.id" @click="exportShop">
                    <VIcon>mdi-export</VIcon> Export
                  </VBtn>
                </VCol>
              </VRow>
            </VCardActions>
          </VCard>
        </template>
        <p v-else><em>No shop selected.</em></p>
      </VCol>
      <VCol cols="12" lg="6" order="12" order-lg="1">
        <VBtn block @click="newShop">
          <VIcon>mdi-plus</VIcon> New shop
        </VBtn>
        <VDataIterator :items="shops" :itemsPerPage="5">
          <template v-slot:default="{ items }">
            <VCard v-for="shop in items" :key="shop.id" class="my-2" @click="getShop(shop.id)">
              <VImg src="/images/placeholder.png" height="100" />
              <VCardTitle>{{shop.name}}</VCardTitle>
              <VCardSubtitle>{{shop.address}} - {{shop.phoneNumber}}</VCardSubtitle>
              <VCardText>{{shop.shortDesc}}</VCardText>
            </VCard>
          </template>
        </VDataIterator>
      </VCol>
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol, VForm, VTextField, VTextarea, VBtn, VCheckbox, VDataIterator, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VIcon, VImg } from 'vuetify/lib';

export default {
  name: 'YourShops',
  data: () => ({
    /** @type {any | null} */ selectedShop: null,
    shops: []
  }),
  methods: {
    newShop() {
      this.selectedShop = {
        id: null,
        name: '',
        address: '',
        phoneNumber: '',
        description: '',
        disabled: true,
      }
    },
    saveShop() {
      let body = {
        id: this.selectedShop.id,
        name: this.selectedShop.name,
        address: this.selectedShop.address,
        phoneNumber: this.selectedShop.phoneNumber,
        description: this.selectedShop.description,
        disabled: this.selectedShop.disabled,
      };
      if (this.selectedShop.id == null) {
        fetch('/api/shop/user/create.php', {
          method: "POST",
          body: JSON.stringify(body),
        })
          .then(res => res.json())
          .then(json => {
            this.selectedShop.id = json.id;
            this.getShops();
          });
      } else {
        fetch('/api/shop/user/edit.php', {
          method: "POST",
          body: JSON.stringify(body),
        })
          .then(res => res.json())
          .then(json => this.getShops());
      }
    },
    exportShop() {
      let link = document.createElement("a");
      link.download = "shop.xml";
      link.href = `/api/shop/user/export.php?id=${this.selectedShop.id}`;
      link.click();
    },
    getShops() {
      fetch('/api/shop/user/all.php')
        .then(res => res.json())
        .then(json => this.shops = json.shops);
    },
    getShop(id) {
      fetch(`/api/shop/get.php?id=${id}`)
        .then(res => res.json())
        .then(json => this.selectedShop = json);
    },
  },
  mounted() {
    this.getShops();
  },
  components: { VRow, VCol, VForm, VTextField, VBtn, VCheckbox, VDataIterator, VCard, VCardTitle, VCardSubtitle, VCardText, VTextarea, VCardActions, VIcon, VImg },
};
</script>