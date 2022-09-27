<template>
  <div class="forgot-password">
    <VRow>
      <VSpacer></VSpacer>
      <VCol cols="12" sm="10" md="8" lg="6">
        <VCard class="py-5 px-10">
          <template v-if="resetToken">
            <VForm v-model="inputs.resetForm">
              <p>Choose a new password</p>
              <VTextField label="New password" type="password" v-model="inputs.newPassword" :rules="[rules.required]">
              </VTextField>
              <VTextField ref="confirmPassword" label="Confirm password" type="password"
                v-model="inputs.confirmPassword"
                :rules="[rules.required, v => v == inputs.newPassword || 'Passwords must be the same']"></VTextField>
              <VBtn block class="my-5" @click="() => $router.push('/login/')" :disabled="!inputs.resetForm">Confirm
              </VBtn>
            </VForm>
          </template>
          <template v-else-if="!sent">
            <VForm v-model="inputs.emailForm">
              <p>Please input the e-mail address you used to register on the site</p>
              <VTextField label="E-mail" type="email" hide-details v-model="inputs.email"
                :rules="[rules.required, rules.email.format]"></VTextField>
              <VBtn block class="my-5" @click="checkEmail" :disabled="!inputs.emailForm">Send e-mail</VBtn>
            </VForm>
          </template>
          <template v-else>
            <p>A link has been sent to your e-mail address!</p>
            <VBtn block class="my-5" @click="sendEmail">Resend e-mail</VBtn>
          </template>
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
  name: 'ForgotPassword',
  data: () => ({
    sent: false,
    resetToken: "",
    inputs: {
      resetForm: false,
      newPassword: '',
      confirmPassword: '',

      emailForm: false,
      email: '',
    },
    rules: {
      required: v => !!v || "Required",
      email: {
        format: v => (/^.+?@.+?\..+?$/).test(v) || "Invalid e-mail address"
      },
    },
  }),
  watch: {
    'inputs.newPassword': function (newVal) {
      this.$refs.confirmPassword.validate();
    }
  },
  methods: {
    checkEmail() {
      // TODO
      this.sendEmail();
    },
    sendEmail() {
      this.sent = true;
    },
  },
  mounted() {
    if (typeof this.$route.query.resetToken == "string") {
      this.resetToken = this.$route.query.resetToken;
    }
  },
  components: { VRow, VCol, VSpacer, VCard, VForm, VTextField, VBtn, VDivider, RouterLink },
};
</script>
