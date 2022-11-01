<template>
  <VMenu ref="menu" v-model="menu" :close-on-content-click="false" transition="scale-transition" offset-y
    min-width="auto">
    <template v-slot:activator="{ on, attrs }">
      <VTextField :value="date" :label="label" :dense="dense" prepend-icon="mdi-calendar" readonly v-bind="attrs"
        v-on="on" :placeholder="placeholder" :persistent-placeholder="persistentPlaceholder" :clearable="clearable">
      </VTextField>
    </template>
    <VDatePicker v-model="date">
      <VSpacer></VSpacer>
      <VBtn text color="primary" @click="menu = false">
        Cancel
      </VBtn>
      <VBtn text color="primary" @click="$refs.menu.save(date)">
        OK
      </VBtn>
    </VDatePicker>
  </VMenu>
</template>

<script>
import { VMenu, VDatePicker, VBtn, VSpacer, VTextField } from 'vuetify/lib';

export default {
  name: 'DateInput',
  props: ['label', 'dense', 'value', 'placeholder', 'persistentPlaceholder', 'clearable'],
  data: () => ({
    date: null,
    menu: false,
  }),
  watch: {
    value() {
      this.date = this.value;
    },
    date(newVal) {
      this.$emit('input', newVal);
    }
  },
  components: { VMenu, VDatePicker, VBtn, VSpacer, VTextField }
}
</script>