<template>
  <div class="manage-users">
    <h1>Users</h1>
    <VRow>
      <VCol cols="12" lg="6" order="1" order-lg="12">
        <template v-if="selectedUser">
          <VCard>
            <VCardTitle>
              <VTextField label="Display Name" v-model="selectedUser.displayName"></VTextField>
            </VCardTitle>
            <VCardText>
              <VTextField label="E-Mail" v-model="selectedUser.email"></VTextField>
              <VSelect label="Role" :items="roleOptions" item-text="name" item-value="value"
                v-model="selectedUser.role"></VSelect>
              <VTextField label="Password" type="password" v-model="selectedUser.password"></VTextField>
            </VCardText>
            <VCardActions>
              <VBtn block @click="saveUser">
                <VIcon>mdi-floppy</VIcon> Save
              </VBtn>
            </VCardActions>
          </VCard>
        </template>
        <p v-else><em>No user selected.</em></p>
      </VCol>
      <VCol cols="12" lg="6" order="12" order-lg="1">
        <VBtn block @click="newUser">
          <VIcon>mdi-plus</VIcon> New user
        </VBtn>
        <VDataIterator :items="users" :itemsPerPage="5">
          <template v-slot:default="{ items }">
            <VCard v-for="user in items" :key="user.id" class="my-2" @click="getUser(user.id)">
              <VCardTitle>{{user.displayName}}</VCardTitle>
              <VCardSubtitle>{{user.email}} - {{user.role}}</VCardSubtitle>
            </VCard>
          </template>
        </VDataIterator>
      </VCol>
    </VRow>
  </div>
</template>


<script>
import { VRow, VCol, VForm, VTextField, VTextarea, VBtn, VCheckbox, VFileInput, VDataIterator, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VIcon, VImg } from 'vuetify/lib';

export default {
  name: 'ManageUsers',
  data: () => ({
    /** @type {any | null} */ selectedUser: null,
    users: [],
    roleOptions: [
      { name: "User", value: "user" },
      { name: "Employee", value: "employee" },
      { name: "Admin", value: "admin" },
    ]
  }),
  methods: {
    newUser() {
      this.selectedUser = {
        id: null,
        email: '',
        displayName: '',
        role: '',
        password: '',
      }
    },
    saveUser() {
      let body = {
        id: this.selectedUser.id,
        email: this.selectedUser.email,
        displayName: this.selectedUser.displayName,
        role: this.selectedUser.role,
        password: this.selectedUser.password,
      };
      if (this.selectedUser.id == null) {
        fetch('/api/user/create.php', {
          method: "POST",
          body: JSON.stringify(body),
        })
          .then(res => res.json())
          .then(json => {
            this.selectedUser.id = json.id;
            this.getUsers();
          });
      } else {
        fetch('/api/user/edit.php', {
          method: "POST",
          body: JSON.stringify(body),
        })
          .then(res => res.json())
          .then(json => this.getUsers());
      }
    },
    getUsers() {
      fetch('/api/user/all.php')
        .then(res => res.json())
        .then(json => this.users = json.users);
    },
    getUser(id) {
      fetch(`/api/user/get.php?id=${id}`)
        .then(res => res.json())
        .then(json => this.selectedUser = json);
    },
  },
  mounted() {
    this.getUsers();
  },
  componets: { VRow, VCol, VForm, VTextField, VTextarea, VBtn, VCheckbox, VFileInput, VDataIterator, VCard, VCardTitle, VCardSubtitle, VCardText, VCardActions, VIcon, VImg }
}
</script>