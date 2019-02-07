<template>
  <div class="flexible-input-container">
    <label class="input-label" :for="id">
      {{ data.label }}
      <span class="input-label__required" v-if="data.required === true">*</span>
    </label>
    <div
      class="input-control__container"
      v-bind:class="{ 'has-adornment-start': data.adornment_start, 'has-adornment-end': data.adornment_end }"
    >
      <span
        class="input-control__adornment adornment-start"
        v-if="data.adornment_start"
      >{{ data.adornment_start }}</span>
      <input
        :id="id"
        :type="type"
        class="input-control"
        :value="data.value || ''"
        :placeholder="data.placeholder || ''"
        :required="data.required === true"
        v-on:change="handleChange($event.target.value)"
      >
      <span
        class="input-control__adornment adornment-end"
        v-if="data.adornment_end"
      >{{ data.adornment_end }}</span>
    </div>
  </div>
</template>

<script>
const types = {
  text: "text",
  number: "number",
  email: "email",
  url: "url"
};
export default {
  name: "flexible-input-normal",
  props: ["id", "data", "onChange"],
  data() {
    const { type } = this.data;
    return {
      loading: false,
      type: types[type] || "text"
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
.input-control {
  flex-grow: 1;
  &__container {
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    align-items: stretch;

    &.has-adornment-start {
      .input-control {
        border-top-left-radius: 0px;
        border-bottom-left-radius: 0px;
      }
    }
    &.has-adornment-end {
      .input-control {
        border-top-right-radius: 0px;
        border-bottom-right-radius: 0px;
      }
    }
  }
  &__adornment {
    background: #bacad6;
    color: #ffffff;
    border-radius: 3px 0 0 3px;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0 10px;
  }
}
</style>
