<template>
  <div class="your-shops">
    <h1>Your shops</h1>
    <VRow>
      <VCol cols="12" lg="6" order="1" order-lg="12">
        <template v-if="selectedShop">
          <VCard>
            <VImg :src="LogoImg" height="300" /> <!-- TODO: Change to map -->
            <VCardTitle>
              <VTextField label="Name" v-model="selectedShop.name"></VTextField>
            </VCardTitle>
            <VCardSubtitle>
              <VTextField label="Address" v-model="selectedShop.address"></VTextField>
              <VTextField label="Phone Number" v-model="selectedShop.phoneNumber"></VTextField>
            </VCardSubtitle>
            <VCardText>
              <VTextarea label="Description" v-model="selectedShop.desc"></VTextarea>
            </VCardText>
            <VCardActions>
              <VBtn block @click="() => selectedShop = null">
                <VIcon>mdi-floppy</VIcon> Save
              </VBtn>
            </VCardActions>
          </VCard>
        </template>
        <p v-else><em>No shop selected.</em></p>
      </VCol>
      <VCol cols="12" lg="6" order="12" order-lg="1">
        <VBtn block>
          <VIcon>mdi-plus</VIcon> New shop
        </VBtn>
        <VDataIterator :items="shops" :itemsPerPage="5">
          <template v-slot:default="{ items }">
            <VCard v-for="shop in items" :key="shop.id" class="ma-2" @click="getShop(shop.id)">
              <VImg :src="LogoImg" height="100" /> <!-- TODO: Change to map -->
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
import { VRow, VCol, VForm, VTextField, VTextarea, VBtn, VDataIterator, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VIcon, VImg } from 'vuetify/lib';
import LogoImg from '../../assets/logo.png';

export default {
  name: 'YourShops',
  data: () => ({
    /** @type {any | null} */ selectedShop: null,
    shops: [],
    LogoImg
  }),
  methods: {
    getShops() {
      fetch('/api/shop/user/all.php')
        .then(res => res.json())
        .then(json => this.shops = json);
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
  components: { VRow, VCol, VForm, VTextField, VBtn, VDataIterator, VCard, VCardTitle, VCardSubtitle, VCardText, VTextarea, VCardActions, VIcon, VImg },
};
</script>