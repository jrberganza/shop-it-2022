<template>
  <div class="login">
    <VRow>
      <VSpacer></VSpacer>
      <VCol cols="12" sm="10" md="8" lg="6">
        <VCard class="py-5 px-10">
          <VForm v-model="inputs.registerForm">
            <VTextField label="E-mail" type="email" v-model="inputs.email"
              :rules="[rules.required, rules.email.format]"></VTextField>
            <VTextField label="Display name" type="text" v-model="inputs.displayName" :rules="[rules.required]">
            </VTextField>
            <VRow>
              <VCol cols="6">
                <VTextField label="Password" type="password" v-model="inputs.password" :rules="[rules.required]">
                </VTextField>
              </VCol>
              <VCol cols="6">
                <VTextField ref="confirmPassword" label="Confirm Password" type="password"
                  v-model="inputs.confirmPassword"
                  :rules="[rules.required, v => v == inputs.password || 'Passwords must be the same']"></VTextField>
              </VCol>
            </VRow>
            <VBtn block class="my-5" @click="register" :disabled="!inputs.registerForm">Register</VBtn>
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
  name: 'Register',
  data: () => ({
    inputs: {
      registerForm: false,
      email: '',
      displayName: '',
      password: '',
      confirmPassword: '',
    },
    rules: {
      required: v => !!v || "Required",
      email: {
        format: v => (/^.+?@.+?\..+?$/).test(v) || "Invalid e-mail address"
      },
    },
  }),
  watch: {
    'inputs.password': function (newVal) {
      this.$refs.confirmPassword.validate();
    }
  },
  methods: {
    register() {
      fetch("/api/session/register.php", {
        method: "POST", body: JSON.stringify({
          email: this.inputs.email,
          displayName: this.inputs.displayName,
          password: this.inputs.password
        })
      })
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
