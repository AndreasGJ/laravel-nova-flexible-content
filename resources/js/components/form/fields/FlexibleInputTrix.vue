<template>
  <div class="flexible-input-container input-trix">
    <label class="input-label" :for="id">
      {{ data.label }}
      <span class="input-label__required" v-if="data.required === true">*</span>
    </label>
    <trix-editor
      :id="id"
      :ref="id"
      @trix-change="handleChange"
      @trix-initialize="initialize"
      :value="data.value || ''"
      :placeholder="data.placeholder || ''"
      :required="data.required === true"
      class="trix-content"
    />
  </div>
</template>

<script>
export default {
  name: "flexible-input-trix",
  props: ["id", "data", "onChange"],
  data() {
    return {
      loading: false,
      hasError: false
    };
  },
  methods: {
    initialize(a, b) {
      const id = this.id;
      const { value } = this.data;
      this.$refs[id].editor.insertHTML(value || "");
    },
    handleChange(evt) {
      const id = this.id;
      const newValue = this.$refs[id].value;
      this.onChange(newValue);
    }
  }
};
</script>

<style lang="scss">
.trix-content {
  background: #ffffff;
}
</style>
