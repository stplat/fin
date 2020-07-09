<template>
  <div class="table">
    <div class="table__unit">млн.руб. без НДС</div>
    <table>
      <tr class="not-hover">
        <th rowspan="2">ДКРЭ/ВД</th>
        <th rowspan="2">Новые закупаемые</th>
        <th colspan="5">Топливо</th>
        <th rowspan="2">ИТОГО:</th>
      </tr>
      <tr class="not-hover">
        <th>ИТОГО:</th>
        <th>Дизельное топливо</th>
        <th>Мазут</th>
        <th>Уголь</th>
        <th>Другие виды топлива<br/>(бензин и газ)</th>
      </tr>
      <template v-for="budget in budgets">
        <tr class="strong bg">
          <td>{{ budget.dkre }}</td>
          <td>{{ budget['63430'] }}</td>
          <td>{{ addWithEmptyHelper([ budget['63310'], budget['63320'], budget['63330'], budget['63340'] ]) }}</td>
          <td>{{ budget['63310'] }}</td>
          <td>{{ budget['63320'] }}</td>
          <td>{{ budget['63330'] }}</td>
          <td>{{ budget['63340'] }}</td>
          <td>{{ addWithEmptyHelper([ budget['63310'], budget['63320'], budget['63330'], budget['63340'],
            budget['63430'] ]) }}
          </td>
        </tr>
        <tr v-for="(vd, key) in budget.vid_deyatelnosti">
          <td>{{ key | converVdHelper }}</td>
          <td>{{ vd['63430'] }}</td>
          <td>{{ addWithEmptyHelper([ vd['63310'], vd['63320'], vd['63330'], vd['63340'] ]) }}</td>
          <td>{{ vd['63310'] }}</td>
          <td>{{ vd['63320'] }}</td>
          <td>{{ vd['63330'] }}</td>
          <td>{{ vd['63340'] }}</td>
          <td>{{ addWithEmptyHelper([ vd['63310'], vd['63320'], vd['63330'], vd['63430'] ]) }}</td>
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
      budgets() {
        return this.$store.getters['budget/getBudget'].budget;
      }
    },
    methods: {},
    mounted() {
      this.$store.dispatch('budget/updateBudget', { period: 1, version: 2, isDkre: 1 })
    }
  }
</script>
