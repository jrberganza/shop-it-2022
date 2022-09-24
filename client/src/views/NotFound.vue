<template>
  <div class="not-found">
    <p>Page not found</p>
    <VBtn @click="() => $router.go(-1)">Go back</VBtn>
  </div>
</template>

<script>
import { VBtn } from 'vuetify/lib';

export default {
  name: 'NotFound',
  data: () => ({}),
  components: { VBtn },
};
</script>
