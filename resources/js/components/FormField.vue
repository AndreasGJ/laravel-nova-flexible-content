<template>
  <div class="flex border-b border-40">
    <div class="w-1/5 py-6 px-8">
      <label class="inline-block text-80 pt-2 leading-tight">{{field.indexName}}</label>
    </div>
    <div class="py-6 px-8 w-4/5">
      <layout-list
        :rows="rows"
        :onDelete="deleteRow"
        :onChange="handleChange"
        :onDirection="handleDirection"
      ></layout-list>
      <layout-selector :show="showLayouts" :layouts="layouts" :onClick="handleClick"></layout-selector>
      <button
        class="btn btn-default btn-primary"
        @click.prevent="toggleLayouts"
        v-text="addButtonText"
        :class="{ 'cursor-not-allowed opacity-50': hasReachedMaximumRows }"
      ></button>
    </div>
  </div>
</template>

<script>
import LayoutList from "./form/LayoutList";
import LayoutSelector from "./form/LayoutSelector";
import { FormField, HandlesValidationErrors } from "laravel-nova";

export default {
  mixins: [FormField, HandlesValidationErrors],
  components: { LayoutSelector, LayoutList },
  data: () => ({
    value: "",
    layouts: [],
    showLayouts: false,
    rows: []
  }),
  props: ["resourceName", "resourceId", "field"],
  computed: {
    addButtonText() {
      return this.field.add_button_text
        ? this.field.add_button_text
        : "Add new content";
    },
    hasReachedMaximumRows() {
      if (this.field.maximum_rows) {
        return this.rows.length >= this.field.maximum_rows;
      }
      return false;
    },
    shouldAddInitialRows() {
      return (
        this.field.initial_rows && this.field.initial_rows > this.rows.length
      );
    }
  },
  methods: {
    handleDirection(index, dir) {
      const el = this.rows[index];
      const rows = this.rows;

      if (dir === "up") {
        const up = rows[index - 1];
        if (up) {
          rows[index - 1] = el;
          rows[index] = up;
        }
      } else {
        const down = rows[index + 1];
        if (down) {
          rows[index + 1] = el;
          rows[index] = down;
        }
      }
      this.rows = [...rows];
    },
    handleClick(layout) {
      this.rows.push(layout);
      this.showLayouts = false;
    },
    handleChange(index, rowData = {}) {
      this.rows = this.rows.map((x, i) =>
        i === index ? { ...x, ...rowData } : x
      );
    },
    toggleLayouts() {
      this.showLayouts = !this.showLayouts;
    },
    setInitialValue() {
      this.value = this.field.value || "";
      const layouts = this.field.layouts || [];
      this.layouts = layouts;

      this.$nextTick(() => {
        const itemValues = this.value ? JSON.parse(this.value) : [];

        const rows = [];
        itemValues.forEach(({ type, data = {} }) => {
          const l = layouts.find(x => x.name === type);

          rows.push({
            ...l,
            data,
            fields: l.fields.map(b => ({
              ...b,
              value: data[b.name] || ""
            }))
          });
        });
        this.rows = rows;
      });
    },
    fill(formData) {
      formData.append(this.field.attribute, this.value || "");
    },
    deleteRow(index) {
      this.rows.splice(index, 1);
    }
  },
  watch: {
    rows: {
      handler: function(newRows) {
        this.value = JSON.stringify(
          newRows.map(({ name, fields = [] }) => {
            const rowData = {};
            fields.forEach(f => {
              rowData[f.name] = f.value || "";
            });
            return {
              type: name,
              data: rowData
            };
          })
        );
      },
      deep: true
    }
  }
};
</script>

<style lang="scss">
</style>
