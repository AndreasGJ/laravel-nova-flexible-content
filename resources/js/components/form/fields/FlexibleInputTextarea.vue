<template>
  <div class="flexible-input-container input-textarea">
    <label class="input-label" :for="id">
      {{ data.label }}
      <span class="input-label__required" v-if="data.required === true">*</span>
    </label>
    <textarea
      :id="id"
      class="input-control"
      :rows="data.rows || 3"
      :value="data.value || ''"
      :placeholder="data.placeholder || ''"
      :required="data.required === true"
      v-on:change="handleChange($event.target.value)"
    ></textarea>
  </div>
</template>

<script>
export default {
  name: "flexible-input-textarea",
  props: ["id", "data", "onChange"],
  data() {
    return {
      loading: false
    };
  },
  methods: {
    handleChange(value) {
      this.onChange(value);
    }
  }
};
</script>

<style lang="scss">
</style>
