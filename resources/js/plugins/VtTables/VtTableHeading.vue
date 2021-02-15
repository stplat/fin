<template>
  <th :class="props.thAttrs['class']" :titel="props.thAttrs.title" :tabindex="props.thAttrs.tabIndex" @keypress="props.thEvents.keypress" @click="props.thEvents.click">
    <div class="VueTables__heading" :title="props.title">
      <div><template v-if="typeof props.heading === 'string'">
        {{ props.heading }}
      </template></div>
      <VtSortControl :props="props"></VtSortControl>
      <vnodes :vnodes="props.heading"/>
    </div>
  </th>
</template>

<script>
  import VtSortControl from 'vue-tables-2/compiled/components/VtSortControl';

  export default {
    name: 'VtTableHeading',
    props: {
      props: Object
    },
    components: {
      VtSortControl,
      vnodes: {
        functional: true,
        render: (h, ctx) => ctx.props.vnodes
      }
    }
  }
</script>
<style lang="scss" scoped>
  .VueTables__heading {
    display: flex;
    justify-content: space-between;
    white-space: nowrap;
  }
</style>
