<template>
  <div class="photo-input">
    <VImg :src="imageIds[0] ? '/api/photo/get.php?id=' + imageIds[0] : '/images/placeholder.png'" height="250"></VImg>
    <VRow class="my-1">
      <VCol cols="12" sm="8">
        <VFileInput block dense hide-details label="Upload image" v-model="imageInput"></VFileInput>
      </VCol>
      <VCol cols="12" sm="4">
        <VBtn block :disabled="!imageInput" @click="uploadImage">
          <VIcon>mdi-upload</VIcon> Upload
        </VBtn>
      </VCol>
    </VRow>
  </div>
</template>

<script>
import { VRow, VCol, VBtn, VImg, VFileInput, VIcon } from 'vuetify/lib';

export default {
  name: 'PhotoInput',
  props: ['value', 'multiple'],
  data: () => ({
    imageInput: null,
    imageIds: [],
  }),
  watch: {
    value(newVal) {
      if (!Array.isArray(newVal)) {
        this.imageIds = [newVal];
      } else {
        this.imageIds = newVal;
      }
    },
    imageIds(newVal) {
      if (this.multiple) {
        this.$emit("input", newVal);
      } else {
        this.$emit("input", newVal[0]);
      }
    },
  },
  methods: {
    uploadImage() {
      if (this.imageInput) {
        fetch("/api/photo/save.php", {
          method: "POST",
          body: this.imageInput,
        })
          .then(res => res.json())
          .then(json => {
            if (json.success) {
              this.imageIds = [json.id];
            }
          });
      }
    }
  },
  mounted() {
    if (!Array.isArray(this.value)) {
      this.imageIds = [this.value];
    } else {
      this.imageIds = this.value;
    }
  },
  components: { VRow, VCol, VBtn, VImg, VFileInput, VIcon }
}
</script>