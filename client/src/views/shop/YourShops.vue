<template>
  <div class="your-shops">
    <h1>Your shops</h1>
    <VRow>
      <VCol cols="12" lg="4">
        <VVirtualScroll :items="shops" :itemHeight="320" height="500" bench="1">
          <template v-slot:default="{ item }">
            <VCard class="mx-2" height="300" @click="() => selectedShop = item">
              <img src="@/assets/logo.png" width="100%" height="100" /> <!-- TODO: Change to map -->
              <VCardTitle>{{item.name}}</VCardTitle>
              <VCardSubtitle>{{item.address}} - {{item.phoneNumber}}</VCardSubtitle>
              <VCardText>{{item.shortDesc}}</VCardText>
            </VCard>
          </template>
        </VVirtualScroll>
        <VBtn block>
          <VIcon>mdi-plus</VIcon> New shop
        </VBtn>
      </VCol>
      <VCol cols="12" lg="8">
        <template v-if="selectedShop">
          <VForm>
            <VTextField label="Name" v-model="selectedShop.name"></VTextField>
            <VTextField label="Address" v-model="selectedShop.address"></VTextField>
            <VTextField label="Phone Number" v-model="selectedShop.phoneNumber"></VTextField>
            <VTextField label="Description" v-model="selectedShop.desc"></VTextField>
            <VBtn block @click="() => selectedShop = null">
              <VIcon>mdi-floppy</VIcon> Save
            </VBtn>
          </VForm>
        </template>
        <template v-else><em>No shop selected.</em></template>
      </VCol>
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol, VForm, VTextField, VBtn, VVirtualScroll, VCard, VCardTitle, VCardSubtitle, VCardText, VIcon } from 'vuetify/lib';

export default {
  name: 'YourShops',
  data: () => ({
    /** @type {any | null} */ selectedShop: null,
    shops: [
      {
        id: 1,
        name: 'tienda1',
        address: 'aquí',
        phoneNumber: '1234-5678',
        desc: 'example shop 1',
      },
      {
        id: 2,
        name: 'tienda2',
        address: 'allá',
        phoneNumber: '5678-1234',
        desc: 'example shop 2',
      },
      {
        id: 2,
        name: 'tienda2',
        address: 'allá',
        phoneNumber: '5678-1234',
        desc: 'example shop 2',
      },
      {
        id: 2,
        name: 'tienda2',
        address: 'allá',
        phoneNumber: '5678-1234',
        desc: 'example shop 2',
      },
      {
        id: 2,
        name: 'tienda2',
        address: 'allá',
        phoneNumber: '5678-1234',
        desc: 'example shop 2',
      },
      {
        id: 2,
        name: 'tienda2',
        address: 'allá',
        phoneNumber: '5678-1234',
        desc: 'example shop 2',
      },
      {
        id: 2,
        name: 'tienda2',
        address: 'allá',
        phoneNumber: '5678-1234',
        desc: 'example shop 2',
      },
      {
        id: 2,
        name: 'tienda2',
        address: 'allá',
        phoneNumber: '5678-1234',
        desc: 'example shop 2',
      },
    ]
  }),
  components: { VRow, VCol, VForm, VTextField, VBtn, VVirtualScroll, VCard, VCardTitle, VCardSubtitle, VCardText, VIcon },
};
</script>