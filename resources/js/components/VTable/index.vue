<template>
  <div class="v-table">
    <div class="v-table__head">
      <div class="v-table__cell"
           v-for="(cell, key) in computedHead"
           :style="cellStyle(cell)">РДКРЭ
      </div>
    </div>
    <div class="v-table__row">
      <div class="v-table__cell">asd</div>
      <div class="v-table__cell">asd</div>
      <div class="v-table__cell">asd</div>
      <div class="v-table__cell">asd</div>
      <div class="v-table__cell">asd</div>
    </div>
  </div>
</template>
<script>
  export default {
    data() {
      return {}
    },
    computed: {
      computedHead() {
        return [
          { name: '', col: '3', row: '3' }
        ]
      }
    },
    methods: {
      cellStyle(cell) {
        console.log(cell)
        return {};
      }
    }
  }
</script>
