<template>
  <div class="report-generator ma-2">
    <div class="my-2">
      <h1>Columns</h1>
      <VCard outlined class="pa-2">
        <template v-if="selectedColumns.length > 0">
          <VChip v-for="i in selectedColumns" :key="generator.columns[i]" class="mx-1">
            {{generator.columns[i].name}}</VChip>
          <VBtn icon @click="() => selectedColumns = []">
            <VIcon>mdi-close</VIcon>
          </VBtn>
        </template>
        <em v-else>No columns selected.</em>
      </VCard>
      <VChipGroup multiple column v-model="selectedColumns" active-class="primary--text">
        <VChip v-for="column in generator.columns" :key="column.column">{{column.name}}</VChip>
      </VChipGroup>
    </div>
    <div class="my-2">
      <h1>Filter</h1>
      <VCard outlined class="pa-4 my-2" v-for="column in generator.columns" :key="column.column">
        <h2>{{column.name}}</h2>
        <template v-if="column.type == 'number'">
          <VSelect label="Type" :items="['-', 'is exactly', 'between']" v-model="column.filter">
          </VSelect>
          <template v-if="column.filter == 'is exactly'">
            <VTextField label="Value"></VTextField>
          </template>
          <template v-else-if="column.filter == 'between'">
            <VTextField label="Start"></VTextField>
            <VTextField label="End"></VTextField>
          </template>
        </template>
        <template v-else-if="column.type == 'text'">
          <VSelect label="Type" :items="['-', 'is exactly']" v-model="column.filter">
          </VSelect>
          <template v-if="column.filter == 'is exactly'">
            <VTextField label="Value"></VTextField>
          </template>
        </template>
        <template v-if="column.type == 'date'">
          <VSelect label="Type" :items="['-', 'is exactly', 'between']" v-model="column.filter">
          </VSelect>
          <template v-if="column.filter == 'is exactly'">
            <DateInput label="Value"></DateInput>
          </template>
          <template v-else-if="column.filter == 'between'">
            <DateInput label="Start"></DateInput>
            <DateInput label="End"></DateInput>
          </template>
        </template>
      </VCard>
    </div>
    <VBtn block>Generate report</VBtn>
  </div>
</template>

<script>
import { VChipGroup, VChip, VCard, VBtn, VIcon, VSelect, VCardTitle, VCardText, VCardActions, VTextField, VDatePicker } from 'vuetify/lib';
import DateInput from '../DateInput.vue';

export default {
  name: 'ReportGenerator',
  props: ['generator'],
  data: () => ({
    /** @type {any[]} */ selectedColumns: [],
  }),
  components: { VChipGroup, VChip, VCard, VBtn, VIcon, VSelect, VCardTitle, VCardText, VCardActions, VTextField, VDatePicker, DateInput },
}
</script>