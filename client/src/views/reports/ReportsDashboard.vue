<template>
  <div class="reports-dashboard">
    <VTabs v-model="tab">
      <VTabsSlider color="accent"></VTabsSlider>
      <VTab v-for="{ name } in generators" :key="name">
        {{ name }}
      </VTab>
    </VTabs>
    <VTabsItems v-model="tab">
      <VTabItem v-for="generator in generators" :key="generator.table">
        <ReportGenerator :generator="generator"></ReportGenerator>
      </VTabItem>
    </VTabsItems>
  </div>
</template>

<script>
import { VCol, VRow, VTabs, VTab, VTabsItems, VTabItem, VDataIterator, VCard, VTabsSlider } from 'vuetify/lib';
import ReportGenerator from '../../components/reports/ReportGenerator.vue';

export default {
  name: 'Dashboard',
  data: () => ({
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
  methods: {},
  components: { VCol, VRow, VTabs, VTab, VTabsItems, VTabItem, VDataIterator, VCard, VTabsSlider, ReportGenerator }
}
</script>