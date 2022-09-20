<template>
  <div class="not-found">
    <p>No se encontró la página</p>
    <VBtn @click="() => $router.go(-1)">Regresar</VBtn>
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
