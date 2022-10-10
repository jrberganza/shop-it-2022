<template>
  <div class="my-shop">
    <h1>My shop</h1>
    <VRow class="my-1">
      <VCol cols="12" sm="8">
        <VFileInput block dense hide-details label="Import XML File" v-model="xmlFile"></VFileInput>
      </VCol>
      <VCol cols="12" sm="4">
        <VBtn block :disabled="!xmlFile" @click="importShop">
          <VIcon>mdi-import</VIcon> Import
        </VBtn>
      </VCol>
      <template v-if="myShop">
        <VCol cols="12" sm="4">
          <VBtn block @click="saveShop">
            <VIcon>mdi-floppy</VIcon> Save
          </VBtn>
        </VCol>
        <VCol cols="12" sm="4">
          <VBtn block :disabled="!myShop.id" @click="() => $router.push(`/my/shop/products`)">
            <VIcon>mdi-shopping</VIcon> Products
          </VBtn>
        </VCol>
        <VCol cols="12" sm="4">
          <VBtn block :disabled="!myShop.id" @click="exportShop">
            <VIcon>mdi-export</VIcon> Export
          </VBtn>
        </VCol>
      </template>
      <VCol cols="12" v-else>
        <VBtn block @click="newShop">
          <VIcon>mdi-plus</VIcon> Create shop
        </VBtn>
      </VCol>
    </VRow>
    <VCard v-if="myShop" class="my-2">
      <VImg
        :src="myShop.photos.length > 0 ? '/api/shop/photo/get.php?id=' + myShop.photos[0] : '/images/placeholder.png'"
        height="250" />
      <VCardTitle>
        <VTextField label="Name" v-model="myShop.name" :rules="[rules.required]" maxlength="255">
        </VTextField>
      </VCardTitle>
      <VCardSubtitle>
        <VTextField label="Address" v-model="myShop.address" :rules="[rules.required]" maxlength="255">
        </VTextField>
        <Map v-model="myShop.location" input></Map>
        <VTextField label="Phone Number" v-model="myShop.phoneNumber"
          :rules="[rules.required, rules.phoneNumber.format]" maxlength="20"></VTextField>
      </VCardSubtitle>
      <VCardText>
        <VTextarea label="Description" v-model="myShop.description" :rules="[rules.required]" counter="512"
          maxlength="512"></VTextarea>
        <VCheckbox label="Disabled?" v-model="myShop.disabled"></VCheckbox>
      </VCardText>
    </VCard>
  </div>
</template>

<script>
import { VRow, VCol, VForm, VTextField, VTextarea, VBtn, VCheckbox, VFileInput, VDataIterator, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VIcon, VImg } from 'vuetify/lib';
import Map from '../../components/map/Map.vue';

export default {
  name: 'MyShop',
  data: () => ({
    /** @type {Blob | null} */ xmlFile: null,
    /** @type {any | null} */ myShop: null,
    rules: {
      required: v => !!v || "Required",
      phoneNumber: {
        format: v => (/^\+?\d+$/).test(v) || "Invalid phone number"
      },
    }
  }),
  methods: {
    newShop() {
      this.myShop = {
        name: '',
        address: '',
        phoneNumber: '',
        location: null,
        description: '',
        disabled: true,
        photos: [],
      }
    },
    saveShop() {
      let body = {
        name: this.myShop.name,
        address: this.myShop.address,
        latitude: this.myShop.location[0],
        longitude: this.myShop.location[1],
        phoneNumber: this.myShop.phoneNumber,
        description: this.myShop.description,
        disabled: this.myShop.disabled,
      };
      if (this.myShop == null) {
        fetch('/api/shop/user/create.php', {
          method: "POST",
          body: JSON.stringify(body),
        })
          .then(res => res.json())
          .then(json => {
            this.myShop.id = json.id;
            this.getShop();
          });
      } else {
        fetch('/api/shop/user/edit.php', {
          method: "POST",
          body: JSON.stringify(body),
        })
          .then(res => res.json())
          .then(json => this.getShop());
      }
    },
    exportShop() {
      let link = document.createElement("a");
      link.download = "shop.xml";
      link.href = `/api/shop/user/export.php`;
      link.click();
    },
    importShop() {
      if (this.xmlFile) {
        let reader = new FileReader();
        reader.onload = () => {
          fetch('/api/shop/user/import.php', {
            method: "POST",
            body: reader.result,
          })
            .then(res => res.json())
            .then(json => this.getShop());
        };
        reader.readAsText(this.xmlFile);
      }
    },
    getShop() {
      fetch(`/api/shop/user/get.php`)
        .then(res => res.json())
        .then(json => {
          this.myShop = json;
          this.myShop.location = [json.latitude, json.longitude];
        });
    },
  },
  mounted() {
    this.getShop();
  },
  components: { VRow, VCol, VForm, VTextField, VBtn, VCheckbox, VFileInput, VDataIterator, VCard, VCardTitle, VCardSubtitle, VCardText, VTextarea, VCardActions, VIcon, VImg, Map },
};
</script>