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
    </VRow>
    <VCard v-if="myShop" class="my-2">
      <VCardTitle>
        <VTextField label="Name" v-model="myShop.name" :rules="[rules.required]" maxlength="255">
        </VTextField>
      </VCardTitle>
      <VCardSubtitle>
        <PhotoInput v-model="myShop.photos" multiple />
        <VSelect label="Department" :disabled="departments == null" :items="departments" itemText="name" itemValue="id"
          v-model="myShop.department" maxlength="255">
        </VSelect>
        <VSelect label="Municipalities" :disabled="municipalities == null || myShop.department == null"
          :items="municipalities" itemText="name" itemValue="id" v-model="myShop.municipality" maxlength="255">
        </VSelect>
        <VTextField label="Zone" :disabled="departments == null || municipalities == null" v-model="myShop.zone"
          :rules="[rules.required, rules.number.format]" maxlength="10"></VTextField>
        <Map v-model="myShop.location" input></Map>
        <VTextField label="Phone Number" v-model="myShop.phoneNumber"
          :rules="[rules.required, rules.phoneNumber.format]" maxlength="20"></VTextField>
      </VCardSubtitle>
      <VCardText>
        <VTextarea label="Description" v-model="myShop.description" :rules="[rules.required]" counter="512"
          maxlength="512"></VTextarea>
        <VSelect v-model="myShop.categories" :items="shopCategories" itemText="name" itemValue="id" label="Categories"
          multiple chips></VSelect>
        <VCheckbox label="Disabled?" v-model="myShop.disabled"></VCheckbox>
      </VCardText>
      <VCardText v-if="myShop.id && !myShop.moderated">
        <em>This shop hasn't been checked by a moderator</em>
      </VCardText>
    </VCard>
    <VRow class="my-1" v-if="myShop">
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
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol, VForm, VTextField, VSelect, VTextarea, VBtn, VCheckbox, VFileInput, VDataIterator, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VIcon, VImg } from 'vuetify/lib';
import Map from '../../components/map/Map.vue';
import PhotoInput from '../../components/photo/PhotoInput.vue';
import { mapMutations } from 'vuex';

export default {
  name: 'MyShop',
  data: () => ({
    /** @type {Blob | null} */ xmlFile: null,
    myShop: {
      id: null,
      name: '',
      zone: '',
      municipality: null,
      department: null,
      phoneNumber: '',
      location: null,
      description: '',
      categories: [],
      disabled: true,
      photos: [],
      moderated: false,
    },
    shopCategories: [],
    departments: null,
    municipalities: null,
    rules: {
      required: v => !!v || "Required",
      number: {
        format: v => (/^\d+$/).test(v) || "Invalid zone"
      },
      phoneNumber: {
        format: v => (/^\+?\d+$/).test(v) || "Invalid phone number"
      },
    }
  }),
  watch: {
    ['myShop.department'](newVal) {
      this.getMunicipalities(newVal);
    }
  },
  methods: {
    newShop() {
      this.myShop = {
        id: null,
        name: '',
        zone: '',
        municipality: null,
        department: null,
        phoneNumber: '',
        location: null,
        description: '',
        categories: [],
        disabled: true,
        photos: [],
        moderated: false,
      }
    },
    saveShop() {
      let body = {
        name: this.myShop.name,
        zone: parseInt(this.myShop.zone, 10),
        municipality: parseInt(this.myShop.municipality, 10),
        latitude: this.myShop.location[0],
        longitude: this.myShop.location[1],
        phoneNumber: this.myShop.phoneNumber,
        description: this.myShop.description,
        categories: this.myShop.categories,
        disabled: this.myShop.disabled,
        photos: this.myShop.photos,
      };
      fetch('/api/shop/user/save.php', {
        method: "POST",
        body: JSON.stringify(body),
      })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.myShop.id = json.id;
            this.getShop();
          } else {
            this.openSnackbar({ shown: true, message: json._error });
          }
        });
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
            .then(json => {
              if (json.success) {
                this.getShop()
              } else {
                this.openSnackbar({ shown: true, message: json._error });
              }
            });
        };
        reader.readAsText(this.xmlFile);
      }
    },
    getShop() {
      fetch(`/api/shop/user/get.php`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.myShop = json;
            this.myShop.location = [json.latitude, json.longitude];
          } else {
            this.newShop();
          }
        });
    },
    getShopCategories() {
      fetch(`/api/category/shop/all.php`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.shopCategories = json.categories;
          } else {
            this.openSnackbar({ shown: true, message: json._error });
          }
        });
    },
    getDepartments() {
      fetch(`/api/location/departments.php`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.departments = json.departments;
          } else {
            this.openSnackbar({ shown: true, message: json._error });
          }
        });
    },
    getMunicipalities(department) {
      this.municipalities = null;
      fetch(`/api/location/municipalities.php?department=${department}`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.municipalities = json.municipalities;
          } else {
            this.openSnackbar({ shown: true, message: json._error });
          }
        });
    },
    ...mapMutations(['openSnackbar']),
  },
  mounted() {
    this.getShop();
    this.getShopCategories();
    this.getDepartments();
  },
  components: { VRow, VCol, VForm, VTextField, VSelect, VBtn, VCheckbox, VFileInput, VDataIterator, VCard, VCardTitle, VCardSubtitle, VCardText, VTextarea, VCardActions, VIcon, VImg, Map, PhotoInput },
};
</script>