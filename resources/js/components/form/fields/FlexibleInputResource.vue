<template>
  <div class="flexible-input-container">
    <label class="input-label" :for="id">
      {{ data.label }}
      <span class="input-label__required" v-if="data.required === true">*</span>
    </label>
    <select
      :id="id"
      class="input-resource input-control"
      :multiple="data.multiple === true"
      :required="data.required === true"
      v-on:change="handleChange($event)"
    >
      <option
        value
        v-if="data.placeholder"
        :selected="!data.value && !data.default_value"
      >{{ data.placeholder }}</option>
      <option
        v-for="(option) in options"
        :key="option.value"
        :value="option.value"
        :selected="(data.value && data.multiple && data.value.indexOf(option.value) >= 0) || data.value == option.value"
      >{{ option.label }}</option>
    </select>
  </div>
</template>

<script>
export default {
  name: "flexible-input-resource",
  props: ["id", "data", "onChange"],
  data() {
    const {
      options = [],
      resource_endpoint,
      resource_value: value,
      resource_label: label
    } = this.data;

    return {
      loading: false,
      showOptions: false,
      options: resource_endpoint
        ? []
        : options.map(x => ({ value: x[value], label: x[label] }))
    };
  },
  methods: {
    handleChange(evt) {
      const { multiple = false } = this.data;
      const el = evt.target;
      if (multiple) {
        const values = [];
        if (el.selectedOptions && el.selectedOptions.length > 0) {
          for (let i = 0; i < el.selectedOptions.length; i++) {
            values.push(el.selectedOptions[i].value);
          }
        }
        this.onChange(values);
      } else {
        const value = el.value;

        this.onChange(value);
      }
    }
  }
};
</script>

<style lang="scss">
.input-resource {
  max-height: 300px;
}
</style>
