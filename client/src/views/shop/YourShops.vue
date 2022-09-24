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
              <VTextarea label="Description" v-model="selectedShop.description"></VTextarea>
              <VCheckbox label="Disabled?" v-model="selectedShop.disabled"></VCheckbox>
            </VCardText>
            <VCardActions>
              <VBtn block @click="saveShop">
                <VIcon>mdi-floppy</VIcon> Save
              </VBtn>
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
import { VRow, VCol, VForm, VTextField, VTextarea, VBtn, VCheckbox, VDataIterator, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VIcon, VImg } from 'vuetify/lib';
import LogoImg from '../../assets/logo.png';

export default {
  name: 'YourShops',
  data: () => ({
    /** @type {any | null} */ selectedShop: null,
    shops: [],
    LogoImg
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
      if (this.selectedShop.id == null) {
        fetch('/api/shop/user/create.php', {
          method: "POST",
          body: JSON.stringify({
            id: this.selectedShop.id,
            name: this.selectedShop.name,
            address: this.selectedShop.address,
            phoneNumber: this.selectedShop.phoneNumber,
            description: this.selectedShop.description,
            disabled: this.selectedShop.disabled,
          }),
        })
          .then(res => res.json())
          .then(json => {
            this.selectedShop.id = json.id;
            this.getShops();
          });
      } else {
        fetch('/api/shop/user/edit.php')
          .then(res => res.json())
          .then(json => this.shops = json.shops);
      }
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