<style lang="scss">
  .VueTables__sortable {
    white-space: nowrap;
  }
</style>
<template>
  <span v-if="props.sortable" class="float-right">
    <unicon :name=icon fill="royalblue"/>
  </span>
</template>

<script>
  export default {
    name: 'MySortControl',
    props: ['props'],
    computed: {
      icon() {
        if (!this.props.sortStatus.sorted) return 'sort';
        return this.props.sortStatus.asc ? 'angle-up' : 'angle-down';
      }
    }
  }
</script>
