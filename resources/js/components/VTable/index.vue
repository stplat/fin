<template>
  <div class="v-table">
    <div class="v-table__head" :style="{ 'grid-template-columns': data.template }">
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
      return {
        data: {
          template: 'repeat(5, 1fr)',
          head: [
            { name: '', col: '', orderRow: '', row: '' },
            { name: '', col: '2', orderRow: '', row: '' },
            { name: '', col: '', orderRow: '', row: '' },
            { name: '', col: '', orderRow: '', row: '' },
            { name: '', col: '', orderRow: '', row: '' },
            { name: '', col: '', orderRow: '', row: '' },
            { name: '', col: '', orderRow: '', row: '' },
          ]
        }
      }
    },
    computed: {
      computedHead() {
        console.log(this.data.head.map((item, key) => {
          console.log(key)
          return Object.assign(item, {
            'order': key + 1
          })
        }))
        return this.data.head.map((item, key) => {
          return Object.assign(item, {
            'order': key + 1
          })
        });
      }
    },
    methods: {
      cellStyle(cell) {
        const mergeCol = cell.col ? `${cell.order} / span ${cell.col}` : 'auto';
        const mergeRow = cell.row ? `1 / span ${cell.row}` : 'auto';

        return {
          'grid-column': mergeCol,
          'grid-row': mergeRow,
        };
      }
    },
    mounted() {

    }
  }
</script>
