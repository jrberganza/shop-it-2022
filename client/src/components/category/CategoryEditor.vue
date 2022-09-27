<template>
  <div class="category-editor mx-2 mb-2">
    <VDataTable :items="tempCategories" :itemsPerPage="5" :headers="tableHeaders">
      <template v-slot:item.name="{ item }">
        <VTextField label="Name" v-model="item.name" :rules="[rules.required]"></VTextField>
      </template>
      <template v-slot:item.disabled="{ item }">
        <VCheckbox label="Disabled?" v-model="item.disabled"></VCheckbox>
      </template>
      <template v-slot:item.save>
        <VBtn block>
          <VIcon>mdi-floppy</VIcon> Save
        </VBtn>
      </template>
    </VDataTable>
    <VBtn block>
      <VIcon>mdi-plus</VIcon>
    </VBtn>
  </div>
</template>

<script>
import { VDataTable, VTextField, VCheckbox, VBtn, VIcon } from 'vuetify/lib';

export default {
  name: 'CategoryEditor',
  props: ['categories'],
  data: () => ({
    tableHeaders: [
      { text: 'Name', value: 'name' },
      { text: 'Disabled', value: 'disabled' },
      { text: 'Save', value: 'save' },
    ],
    /** @type {any[]} */ tempCategories: [],
    rules: {
      required: v => !!v || "Required",
    },
  }),
  watch: {
    categories(newVal) {
      this.tempCategories = newVal;
    }
  },
  mounted() {
    this.tempCategories = this.categories;
  },
  components: { VDataTable, VTextField, VCheckbox, VBtn, VIcon }
};
</script>