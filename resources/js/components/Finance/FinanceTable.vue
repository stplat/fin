<template>
  <div class="table">
    <div class="table__unit">млн.руб. без НДС</div>
    <table>
      <tr class="not-hover">
        <th rowspan="2" style="width: 200px">ДКРЭ/СТАТЬЯ</th>
        <th colspan="5">ВСЕГО:</th>
        <th colspan="5">Централизованная закупка</th>
        <th colspan="5">Самостоятельная закупка</th>
        <th colspan="5">Через другие филиалы</th>
      </tr>
      <tr>
        <th>ВСЕГО:</th>
        <th>ПЕР</th>
        <th>ПВД</th>
        <th>КВ</th>
        <th>ПРО</th>
        <th>ИТОГО:</th>
        <th>ПЕР</th>
        <th>ПВД</th>
        <th>КВ</th>
        <th>ПРО</th>
        <th>ИТОГО:</th>
        <th>ПЕР</th>
        <th>ПВД</th>
        <th>КВ</th>
        <th>ПРО</th>
        <th>ИТОГО:</th>
        <th>ПЕР</th>
        <th>ПВД</th>
        <th>КВ</th>
        <th>ПРО</th>
      </tr>
      <template v-for="finance in finances">
        <tr :class="{'strong bg': finance.name === 'ИТОГО'}">
          <td>{{ finance.name }}</td>
          <td  class="strong bg">{{ addWithEmptyHelper([ finance.activity['01']['1'], finance.activity['21']['1'],
            finance.activity['61']['1'], finance.activity['81']['1'], finance.activity['01']['2'], finance.activity['21']['2'],
            finance.activity['61']['2'], finance.activity['81']['2'], finance.activity['01']['3'], finance.activity['21']['3'],
            finance.activity['61']['3'], finance.activity['81']['3'] ]) | roundHelper }}
          </td>
          <td>{{ addWithEmptyHelper([ finance.activity['01']['1'], finance.activity['01']['2'] ]) | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ finance.activity['21']['1'], finance.activity['21']['2'] ]) | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ finance.activity['61']['1'], finance.activity['61']['2'] ]) | roundHelper }}</td>
          <td>{{ addWithEmptyHelper([ finance.activity['81']['1'], finance.activity['81']['2'] ]) | roundHelper }}</td>
          <td class="strong bg">{{ addWithEmptyHelper([ finance.activity['01']['1'], finance.activity['21']['1'],
            finance.activity['61']['1'], finance.activity['81']['1'] ]) | roundHelper }}
          </td>
          <td>{{ finance.activity['01']['1'] | roundHelper }}</td>
          <td>{{ finance.activity['21']['1'] | roundHelper }}</td>
          <td>{{ finance.activity['61']['1'] | roundHelper }}</td>
          <td>{{ finance.activity['81']['1'] | roundHelper }}</td>
          <td class="strong bg">{{ addWithEmptyHelper([ finance.activity['01']['2'], finance.activity['21']['2'],
            finance.activity['61']['2'], finance.activity['81']['2'] ]) | roundHelper }}
          </td>
          <td>{{ finance.activity['01']['2'] | roundHelper }}</td>
          <td>{{ finance.activity['21']['2'] | roundHelper }}</td>
          <td>{{ finance.activity['61']['2'] | roundHelper }}</td>
          <td>{{ finance.activity['81']['2'] | roundHelper }}</td>
          <td class="strong bg">{{ addWithEmptyHelper([ finance.activity['01']['3'], finance.activity['21']['3'],
            finance.activity['61']['3'], finance.activity['81']['3'] ]) | roundHelper }}
          </td>
          <td>{{ finance.activity['01']['3'] | roundHelper }}</td>
          <td>{{ finance.activity['21']['3'] | roundHelper }}</td>
          <td>{{ finance.activity['61']['3'] | roundHelper }}</td>
          <td>{{ finance.activity['81']['3'] | roundHelper }}</td>
        </tr>
      </template>
    </table>
  </div>
</template>

<script>
  export default {
    data() {
      return {}
    },
    computed: {
      finances() {
        return this.$store.getters['finance/getFinances'];
      }
    },
    methods: {},
    mounted() {
    }
  }
</script>
