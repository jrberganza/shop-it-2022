<template>
  <div class="reports-dashboard">
    <VSelect label="Objects" :items="generators" itemText="name" itemValue="table" v-model="report.table"></VSelect>
    <template v-if="report.table">
      <VSelect label="Properties" v-model="report.fields" :items="tableGenerator.columns" itemText="name"
        itemValue="column" multiple chips></VSelect>
      <VCard outlined class="my-2">
        <VCardTitle>Filtering</VCardTitle>
        <VCardText>
          <VCard outlined class="my-2" v-for="(filter, i) in report.filters" :key="i">
            <VCardText>
              <VSelect label="Property" v-model="filter.column" :items="tableGenerator.columns" itemText="name"
                :itemValue="(obj) => obj">
              </VSelect>
              <template v-if="filter.column">
                <VSelect label="Type" :items="filters.filter(f => f.appliesTo.indexOf(filter.column.type) > -1)"
                  itemText="name" itemValue="value" v-model="filter.type">
                </VSelect>
                <template v-if="filter.column.type == 'number'">
                  <template v-if="filter.type == '='">
                    <VTextField label="Value" v-model="filter.value"></VTextField>
                  </template>
                  <template v-else-if="filter.type == '<>'">
                    <VTextField label="Minimum" placeholder="Any" persistent-placeholder clearable
                      v-model="filter.start"></VTextField>
                    <VTextField label="Maximum" placeholder="Any" persistent-placeholder clearable v-model="filter.end">
                    </VTextField>
                  </template>
                </template>
                <template v-else-if="filter.column.type == 'text'">
                  <template v-if="filter.type == '='">
                    <VTextField label="Value" v-model="filter.value"></VTextField>
                  </template>
                  <template v-if="filter.type == 'in'">
                    <VTextField label="Value" v-model="filter.value"></VTextField>
                  </template>
                </template>
                <template v-if="filter.column.type == 'date'">
                  <template v-if="filter.type == '='">
                    <DateInput label="Value" v-model="filter.value"></DateInput>
                  </template>
                  <template v-else-if="filter.type == '<>'">
                    <DateInput label="Minimum" placeholder="Any" persistent-placeholder clearable
                      v-model="filter.start"></DateInput>
                    <DateInput label="Maximum" placeholder="Any" persistent-placeholder clearable v-model="filter.end">
                    </DateInput>
                  </template>
                </template>
                <template v-if="filter.column.type == 'boolean'">
                  <template v-if="filter.type == '='">
                    <VSelect label="Value" :items="[{ name: 'True', value: true }, { name: 'False', value: false }]"
                      itemText="name" itemValue="value" v-model="filter.value"></VSelect>
                  </template>
                </template>
              </template>
            </VCardText>
            <VCardActions>
              <VBtn @click="removeFilter(i)" text>
                <VIcon>mdi-close</VIcon> Remove
              </VBtn>
            </VCardActions>
          </VCard>
        </VCardText>
        <VCardActions>
          <VBtn @click="addFilter" text>
            <VIcon>mdi-plus</VIcon> Add
          </VBtn>
        </VCardActions>
      </VCard>
      <VCard outlined class="my-2">
        <VCardTitle>Ordering</VCardTitle>
        <VCardText>
          <VCard outlined class="my-2" v-for="(order, i) in report.orders" :key="i">
            <VCardText>
              <VSelect label="Property" v-model="order.column" :items="tableGenerator.columns" itemText="name"
                :itemValue="(obj) => obj">
              </VSelect>
              <template v-if="order.column">
                <VSelect label="Type" :items="orders" itemText="name" itemValue="value" v-model="order.type"></VSelect>
              </template>
            </VCardText>
            <VCardActions>
              <VBtn @click="removeOrder(i)" text>
                <VIcon>mdi-close</VIcon> Remove
              </VBtn>
            </VCardActions>
          </VCard>
        </VCardText>
        <VCardActions>
          <VBtn @click="addOrder" text>
            <VIcon>mdi-plus</VIcon> Add
          </VBtn>
        </VCardActions>
      </VCard>
      <VBtn @click="generateReport">Preview report</VBtn>
      <VBtn @click="downloadReport">Download report</VBtn>
    </template>
    <VDataTable v-if="generatedReport" :headers="tableHeaders" :items="generatedReport"></VDataTable>
  </div>
</template>

<script>
import { VCol, VRow, VCard, VBtn, VChipGroup, VChip, VIcon, VCardTitle, VCardText, VCardActions, VDatePicker, VTextField, VSelect, VSwitch, VDataTable } from 'vuetify/lib';
import DateInput from '../../components/DateInput.vue';

export default {
  name: 'Dashboard',
  data: () => ({
    report: {
      table: null,
      fields: [],
      filters: [],
      orders: [],
    },
    filters: [
      { name: 'is exactly', value: '=', appliesTo: ['text', 'number', 'date', 'boolean'] },
      { name: 'contains', value: 'in', appliesTo: ['text'] },
      { name: 'between', value: '<>', appliesTo: ['number', 'date'] },
    ],
    orders: [
      { name: 'Ascending', value: 'asc' },
      { name: 'Descending', value: 'desc' },
    ],
    generators: [],
    generatedReport: null,
    tableHeaders: [],
  }),
  watch: {
    ['report.table']() {
      this.report.fields = [];
      this.report.filters = [];
      this.report.orders = [];
    }
  },
  computed: {
    tableGenerator() {
      return this.generators.filter(t => t.table == this.report.table)[0]
    }
  },
  methods: {
    getGenerators() {
      fetch(`/api/reports/valid.php`)
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.generators = json.generators;
          }
        })
    },
    addFilter() {
      this.report.filters.push({
        column: null,
        type: '',
        value: '',
        start: '',
        end: '',
      })
    },
    removeFilter(i) {
      this.report.filters.splice(i, 1);
    },
    addOrder() {
      this.report.orders.push({
        column: null,
        type: '',
      })
    },
    removeOrder(i) {
      this.report.orders.splice(i, 1);
    },
    generateReport() {
      fetch('/api/reports/generate.php', {
        method: "POST",
        body: JSON.stringify(this.report),
      })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.generatedReport = json.report;
            if (this.report.fields.length > 0) {
              this.tableHeaders = this.report.fields
                .map(f => this.tableGenerator.columns.filter(c => c.column == f)[0])
                .map(f => ({ ...f, text: f.name, value: f.column, sortable: false, }));
            } else {
              this.tableHeaders = this.tableGenerator.columns
                .map(f => ({ ...f, text: f.name, value: f.column, sortable: false, }));
            }
          }
        });
    },
    downloadReport() {
      fetch('/api/reports/generate.php', {
        method: "POST",
        body: JSON.stringify({ ...this.report, csv: true }),
      })
        .then(res => res.blob())
        .then(blob => {
          let url = URL.createObjectURL(blob);
          let link = document.createElement("a");
          link.download = `report_${Math.floor(Date.now() / 1000)}.csv`;
          link.href = url;
          link.click();
          URL.revokeObjectURL(blob);
        });
    }
  },
  mounted() {
    this.getGenerators();
  },
  components: { VCol, VRow, VCard, VBtn, VChipGroup, VChip, VIcon, VCardTitle, VCardText, VCardActions, VDatePicker, VTextField, VSelect, DateInput, VSwitch, VDataTable }
}
</script>