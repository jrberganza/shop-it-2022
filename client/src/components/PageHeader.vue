<template>
  <VToolbar class="toolbar" color="primary" dark>
    <VAppBarNavIcon @click="() => $emit('openDrawer')"></VAppBarNavIcon>
    <VToolbarTitle class="logo py-2">
      <Logo></Logo>
    </VToolbarTitle>
    <VRow>
      <VSpacer></VSpacer>
      <VCol cols="11" md="10" lg="8">
        <VTextField v-model="searchTerm" hide-details @keydown="searchOnEnter">
          <template v-slot:append>
            <VBtn icon small @click="search">
              <VIcon>mdi-magnify</VIcon>
            </VBtn>
          </template>
        </VTextField>
      </VCol>
      <VSpacer></VSpacer>
    </VRow>
    <template v-if="session == null || session.role == 'visitor'">
      <VBtn @click="() => $router.push('/login/')" class="mx-2">Login</VBtn>
      <VBtn @click="() => $router.push('/register/')" class="mx-2">Register</VBtn>
    </template>
    <VBtn v-else @click="logout" class="mx-2">Logout</VBtn>
  </VToolbar>
</template>

<style>
.toolbar {
  position: sticky;
  top: 0;
  z-index: 10000;
}

.logo {
  height: 100%;
}
</style>

<script>
import {
  VToolbar, VAppBarNavIcon, VToolbarTitle, VTextField, VBtn, VIcon, VRow, VCol, VSpacer, VImg, VCard
} from 'vuetify/lib';
import { mapMutations, mapState } from 'vuex';
import Logo from './Logo.vue';

export default {
  name: 'PageHeader',
  data: () => ({
    searchTerm: '',
  }),
  computed: {
    ...mapState(['session', 'searchRequest']),
  },
  methods: {
    search() {
      this.updateSearchRequest({ ...this.searchRequest, query: this.searchTerm });
      this.$router.push(`/search/?q=${encodeURIComponent(this.searchRequest.query)}&shopCategories=${this.searchRequest.shopCategories.map(d => encodeURIComponent(d)).join(",")}&productCategories=${this.searchRequest.productCategories.map(d => encodeURIComponent(d)).join(",")}`);
    },
    searchOnEnter(ev) {
      if (ev.key.toLowerCase() === 'enter') {
        this.search();
      }
    },
    logout() {
      fetch('/api/session/logout.php')
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            this.$store.dispatch('fetchSession');
            this.$router.push('/');
            this.openSnackbar({ shown: true, message: "Logged out" });
          } else {
            this.openSnackbar({ shown: true, message: json._error });
          }
        });
    },
    ...mapMutations(['updateSearchRequest', 'openSnackbar'])
  },
  mounted() {
    if (this.$route.name == "Search") {
      if (typeof this.$route.query.q == "string") {
        this.searchRequest.query = this.$route.query.q;
        this.searchRequest.shopCategories = this.$route.query.shopCategories ? this.$route.query.shopCategories.split(",").map(d => parseInt(d, 10)) : [];
        this.searchRequest.productCategories = this.$route.query.productCategories ? this.$route.query.productCategories.split(",").map(d => parseInt(d, 10)) : [];
        this.updateSearchRequest(this.searchRequest);

        this.searchTerm = this.searchRequest.query;
      }
    }
  },
  components: {
    VToolbar,
    VAppBarNavIcon,
    VToolbarTitle,
    VTextField,
    VBtn,
    VIcon,
    VRow,
    VCol,
    VSpacer,
    VImg,
    VCard,
    Logo
  },
};
</script>
