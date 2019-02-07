<template>
  <span>{{ field.value && rows.length > 0 ? 'Content count: ' + rows.length : '-' }}</span>
</template>

<script>
export default {
  props: ["resourceName", "field"],

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
