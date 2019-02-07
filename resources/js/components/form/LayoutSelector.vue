<template>
  <ul class="layout-selector" v-if="show">
    <li v-for="layout in layouts" :key="layout.name" v-on:click="onClick(layout)">{{ layout.label }}</li>
  </ul>
</template>

<script>
export default {
  name: "layout-selector",
  props: ["show", "layouts", "onClick"]
};
</script>
<style lang="scss">
.layout-selector {
  list-style: none;
  position: relative;
  margin: 0 auto 17px 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: stretch;
  max-width: 400px;

  li {
    cursor: pointer;
    background: #eeeeee;
    padding: 7px 10px;
    border-bottom: 1px solid #ccc;

    &:hover {
      background: #ccc;
    }
  }
  &:before {
    position: absolute;
    content: "";
    left: 5px;
    top: 100%;
    width: 0;
    height: 0;
    display: block;
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    border-top: 10px solid #ccc;
  }
}
</style>
