<template>
  <div v-if="rows && rows.length > 0">
    <ul class="layout-list">
      <li v-for="(row, rowIndex) in rows" :key="row.name" class="layout-list__item layout-item">
        <h4 class="layout-item__title">
          {{ row.label }}
          <button
            class="btn btn-default btn-up"
            v-on:click.prevent="onDirection(rowIndex, 'up')"
            v-bind:class="{ deactivated: 0 === rowIndex }"
          >Up</button>
          <button
            class="btn btn-default btn-down"
            v-on:click.prevent="onDirection(rowIndex, 'down')"
            v-bind:class="{ deactivated: (rows.length - 1) === rowIndex }"
          >Down</button>
          <button class="btn btn-default btn-danger" v-on:click.prevent="onDelete(rowIndex)">Remove</button>
        </h4>
        <div :class="'layout-item__fields' + (row.class ? ' ' + row.class : '')">
          <div
            v-for="(field, fieldIndex) in row.fields.filter(f => showField(f, row))"
            :class="'layout-item__fields__item' + (field.class ? ' ' + field.class : '')"
            :key="fieldIndex + field.name"
          >
            <input-image
              v-if="field.type === 'image'"
              :id="fieldIndex + field.name"
              :data="field"
              :onChange="handleChange(rowIndex, fieldIndex)"
            ></input-image>
            <input-normal
              v-if="['text', 'number', 'email', 'url'].indexOf(field.type) >= 0"
              :id="fieldIndex + field.name"
              :data="field"
              :onChange="handleChange(rowIndex, fieldIndex)"
            ></input-normal>
            <input-textarea
              v-if="field.type === 'textarea'"
              :id="fieldIndex + field.name"
              :data="field"
              :onChange="handleChange(rowIndex, fieldIndex)"
            ></input-textarea>
            <input-select
              v-if="field.type === 'select'"
              :id="fieldIndex + field.name"
              :data="field"
              :onChange="handleChange(rowIndex, fieldIndex)"
            ></input-select>
            <input-trix
              v-if="field.type === 'trix'"
              :id="fieldIndex + field.name"
              :data="field"
              :onChange="handleChange(rowIndex, fieldIndex)"
            ></input-trix>
            <input-resource
              v-if="field.type === 'resource'"
              :id="fieldIndex + field.name"
              :data="field"
              :onChange="handleChange(rowIndex, fieldIndex)"
            ></input-resource>
          </div>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
import FlexibleInputImage from "./fields/FlexibleInputImage";
import FlexibleInputNormal from "./fields/FlexibleInputNormal";
import FlexibleInputTextarea from "./fields/FlexibleInputTextarea";
import FlexibleInputSelect from "./fields/FlexibleInputSelect";
import FlexibleInputTrix from "./fields/FlexibleInputTrix";
import FlexibleInputResource from "./fields/FlexibleInputResource";

export default {
  name: "layout-list",
  components: {
    InputImage: FlexibleInputImage,
    InputNormal: FlexibleInputNormal,
    InputTextarea: FlexibleInputTextarea,
    InputTrix: FlexibleInputTrix,
    InputSelect: FlexibleInputSelect,
    InputResource: FlexibleInputResource
  },
  props: ["rows", "onDelete", "onChange", "onDirection"],

  methods: {
    showField(field, row) {
      const { if: ifLogic } = field;
      const { fields = [] } = row;
      if (ifLogic && typeof ifLogic === "object") {
        return Object.keys(ifLogic).some(k => {
          const v = ifLogic[k];
          const field = fields.find(x => x.name === k);

          if (
            field &&
            field.value &&
            (field.value === v || field.value.indexOf(v) >= 0)
          ) {
            return true;
          }
          return false;
        });
      }
      return true;
    },
    handleChange(rowIndex, fieldIndex) {
      const row = this.rows[rowIndex];

      return value =>
        this.onChange(rowIndex, {
          ...row,
          fields: row.fields.map((f, fi) =>
            fieldIndex === fi ? { ...f, value } : f
          )
        });
    }
  }
};
</script>
<style lang="scss">
.layout-item {
  &__fields {
    &__item {
      padding: 10px 0;
    }
  }
}
</style>

