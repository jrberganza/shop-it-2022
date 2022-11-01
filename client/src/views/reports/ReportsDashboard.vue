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
                    <VTextField label="Value"></VTextField>
                  </template>
                  <template v-else-if="filter.type == '<>'">
                    <VTextField label="Minimum" placeholder="Any" persistent-placeholder clearable></VTextField>
                    <VTextField label="Maximum" placeholder="Any" persistent-placeholder clearable></VTextField>
                  </template>
                </template>
                <template v-else-if="filter.column.type == 'text'">
                  <template v-if="filter.type == '='">
                    <VTextField label="Value"></VTextField>
                  </template>
                  <template v-if="filter.type == 'in'">
                    <VTextField label="Value"></VTextField>
                  </template>
                </template>
                <template v-if="filter.column.type == 'date'">
                  <template v-if="filter.type == '='">
                    <DateInput label="Value"></DateInput>
                  </template>
                  <template v-else-if="filter.type == '<>'">
                    <DateInput label="Minimum" placeholder="Any" persistent-placeholder clearable></DateInput>
                    <DateInput label="Maximum" placeholder="Any" persistent-placeholder clearable></DateInput>
                  </template>
                </template>
                <template v-if="filter.column.type == 'boolean'">
                  <template v-if="filter.type == '='">
                    <VSelect label="Value" :items="[{ name: 'True', value: true }, { name: 'False', value: false }]"
                      itemText="name" itemValue="value"></VSelect>
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
      <VBtn @click="generateReport">Generate report</VBtn>
    </template>
  </div>
</template>

<script>
import { VCol, VRow, VCard, VBtn, VChipGroup, VChip, VIcon, VCardTitle, VCardText, VCardActions, VDatePicker, VTextField, VSelect, VSwitch } from 'vuetify/lib';
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
    generators: [
      {
        name: 'Users',
        table: 'users',
        columns: [
          { name: 'ID', column: 'id', type: 'number' },
          { name: 'E-mail', column: 'email', type: 'text' },
          { name: 'Display name', column: 'display_name', type: 'text' },
          { name: 'Role', column: 'role', type: 'text' },
          { name: 'Created at', column: 'created_at', type: 'date' },
          { name: 'Updated at', column: 'updated_at', type: 'date' },
        ],
      },
      {
        name: 'Products',
        table: 'products',
        columns: [
          { name: 'ID', column: 'id', type: 'number' },
          { name: 'Name', column: 'name', type: 'text' },
          { name: 'Price', column: 'price', type: 'number' },
          { name: 'Description', column: 'description', type: 'text' },
          { name: 'Disabled', column: 'disabled', type: 'boolean' },
          { name: 'Comments', column: 'comments', type: 'number' },
          { name: 'Average rating', column: 'average_rating', type: 'number' },
          { name: 'Shop ID', column: 'shop_id', type: 'number' },
          { name: 'Shop Name', column: 'shop_name', type: 'text' },
          { name: 'Owner ID', column: 'owner_id', type: 'number' },
          { name: 'Owner Name', column: 'owner_name', type: 'text' },
          { name: 'Created at', column: 'created_at', type: 'date' },
          { name: 'Updated at', column: 'updated_at', type: 'date' },
        ],
      },
      {
        name: 'Shops',
        table: 'shops',
        columns: [
          { name: 'ID', column: 'id', type: 'number' },
          { name: 'Name', column: 'name', type: 'text' },
          { name: 'Address', column: 'address', type: 'text' },
          { name: 'Latitude', column: 'latitude', type: 'number' },
          { name: 'Longitude', column: 'longitude', type: 'number' },
          { name: 'Phone number', column: 'phone_number', type: 'text' },
          { name: 'Description', column: 'description', type: 'text' },
          { name: 'Disabled', column: 'disabled', type: 'boolean' },
          { name: 'Total products', column: 'total_products', type: 'number' },
          { name: 'Enabled products', column: 'enabled_products', type: 'number' },
          { name: 'Comments', column: 'comments', type: 'number' },
          { name: 'Average rating', column: 'average_rating', type: 'number' },
          { name: 'Owner ID', column: 'owner_id', type: 'number' },
          { name: 'Owner Name', column: 'owner_name', type: 'text' },
          { name: 'Created at', column: 'created_at', type: 'date' },
          { name: 'Updated at', column: 'updated_at', type: 'date' },
        ],
      },
      {
        name: 'Comments',
        table: 'comments',
        columns: [
          { name: 'ID', column: 'id', type: 'number' },
          { name: 'Author ID', column: 'author_id', type: 'number' },
          { name: 'Author name', column: 'author_name', type: 'text' },
          { name: 'Content', column: 'content', type: 'text' },
          { name: 'Parent comment ID', column: 'parent_comment_id', type: 'number' },
          { name: 'Parent comment author ID', column: 'parent_comment_author_id', type: 'number' },
          { name: 'Parent comment author name', column: 'parent_comment_author_name', type: 'text' },
          { name: 'Replies', column: 'replies', type: 'number' },
          { name: 'Votes', column: 'votes', type: 'number' },
          { name: 'Created at', column: 'created_at', type: 'date' },
        ],
      },
      {
        name: 'Moderation',
        table: 'moderation',
        columns: [
          { name: 'ID', column: 'id', type: 'number' },
          { name: 'Moderator ID', column: 'author_id', type: 'number' },
          { name: 'Moderator name', column: 'author_name', type: 'text' },
          { name: 'Content type', column: 'content_type', type: 'text' },
          { name: 'Content ID', column: 'content_id', type: 'number' },
          { name: 'Content', column: 'content', type: 'text' },
          { name: 'Moderation status', column: 'moderation_status', type: 'text' },
          { name: 'Moderated at', column: 'moderated_at', type: 'date' },
        ],
      },
    ],
    tab: null,
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
            console.log(json);
          }
        });
    }
  },
  components: { VCol, VRow, VCard, VBtn, VChipGroup, VChip, VIcon, VCardTitle, VCardText, VCardActions, VDatePicker, VTextField, VSelect, DateInput, VSwitch }
}
</script>