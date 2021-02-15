<style lang="scss">
  .VueTables__sortable {
    white-space: nowrap;
  }
</style>
<template>
  <div v-if="props.sortable" class="pl-3">
    <unicon :name=icon fill="royalblue"/>
  </div>
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
