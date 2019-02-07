<template>
  <div class="flex border-b border-40">
    <div class="w-1/4 py-4">
      <h4 class="font-normal text-80">{{ field.indexName }}</h4>
    </div>
    <div
      class="w-3/4 py-4"
    >{{ field.value && rows.length > 0 ? 'Content count: ' + rows.length : '-' }}</div>
  </div>
</template>

<script>
export default {
  props: ["resource", "resourceName", "resourceId", "field"],
  data() {
    const { value, layouts = [] } = this.field;
    const itemValues = value ? JSON.parse(value) : [];

    const rows = [];
    itemValues.forEach(({ type, data = {} }) => {
      const l = layouts.find(x => x.name === type);

      rows.push({
        ...l,
        fields: l.fields.map(b => ({
          ...b,
          value: data[b.name] || ""
        }))
      });
    });
    return { rows };
  }
};
</script>
