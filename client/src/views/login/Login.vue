<template>
  <div class="login">
    <VRow>
      <VSpacer></VSpacer>
      <VCol cols="12" sm="10" md="8" lg="6">
        <VCard class="py-5 px-10">
          <VForm v-model="inputs.loginForm">
            <VTextField label="Email" type="text" v-model="inputs.email" :rules="[rules.required, rules.email.format]">
            </VTextField>
            <VTextField label="Password" type="password" v-model="inputs.password" :rules="[rules.required]">
            </VTextField>
            <div class="my-2">
              <RouterLink to="/login/forgot/">Forgot your password?</RouterLink>
            </div>
            <VBtn block class="my-5" @click="login" :disabled="!inputs.loginForm">Login</VBtn>
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
      loginForm: false,
      email: '',
      password: '',
    },
    rules: {
      required: v => !!v || "Required",
      email: {
        format: v => (/^.+?@.+?\..+?$/).test(v) || "Invalid e-mail address"
      },
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
