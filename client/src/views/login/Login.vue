<template>
  <div class="login">
    <VRow>
      <VSpacer></VSpacer>
      <VCol cols="12" sm="10" md="8" lg="6">
        <VCard class="py-5 px-10">
          <VForm>
            <VTextField label="Email" type="text" hide-details v-model="inputs.email"></VTextField>
            <VTextField label="Password" type="password" hide-details v-model="inputs.password"></VTextField>
            <div class="my-2">
              <RouterLink to="/login/forgot/">Forgot your password?</RouterLink>
            </div>
            <VBtn block class="my-5" @click="login">Login</VBtn>
            <VDivider class="mt-5 mb-2"></VDivider>
            <div>
              Don't have an account? <RouterLink to="/register/">Register</RouterLink>
            </div>
          </VForm>
        </VCard>
      </VCol>
      <VSpacer></VSpacer>
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol, VSpacer, VCard, VForm, VTextField, VBtn, VDivider } from 'vuetify/lib';
import { RouterLink } from 'vue-router';

export default {
  name: 'Login',
  data: () => ({
    inputs: {
      email: '',
      password: '',
    },
  }),
  methods: {
    login() {
      fetch("/api/session/login.php", { method: "POST", body: JSON.stringify({ email: this.inputs.email, password: this.inputs.password }) })
        .then(res => res.json())
        .then(json => {
          if (!json.success) {
            return;
          }

          this.$store.dispatch('fetchSession');

          this.$router.push('/');
        })
    }
  },
  components: { VRow, VCol, VSpacer, VCard, VForm, VTextField, VBtn, VDivider, RouterLink },
};
</script>
