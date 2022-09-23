<template>
  <div class="category-editor">
    <VSimpleTable>
      <template v-slot:default>
        <thead>
          <tr>
            <th>
              Name
            </th>
            <th>
              Disabled
            </th>
            <th>
              Save
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="category in categories" :key="category.id">
            <td>
              <VTextField label="Name" v-model="category.name"></VTextField>
            </td>
            <td>
              <VCheckbox label="Disabled?" v-model="category.disabled"></VCheckbox>
            </td>
            <td>
              <VBtn block>
                <VIcon>mdi-floppy</VIcon>
              </VBtn>
            </td>
          </tr>
        </tbody>
      </template>
    </VSimpleTable>
    <VBtn block>
      <VIcon>mdi-plus</VIcon>
    </VBtn>
  </div>
</template>

<script>
import { VSimpleTable, VTextField, VCheckbox, VBtn, VIcon } from 'vuetify/lib';

export default {
  name: 'CategoryEditor',
  props: ['categories'],
  components: { VSimpleTable, VTextField, VCheckbox, VBtn, VIcon }
};
</script>