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
          <td>{{ budget.total['63430'] }}</td>
          <td>{{ addWithEmptyHelper([ budget.total['63310'], budget.total['63320'], budget.total['63330'],
            budget.total['63340'] ]) }}
          </td>
          <td>{{ budget.total['63310'] }}</td>
          <td>{{ budget.total['63320'] }}</td>
          <td>{{ budget.total['63330'] }}</td>
          <td>{{ budget.total['63340'] }}</td>
          <td>{{ addWithEmptyHelper([ budget.total['63310'], budget.total['63320'], budget.total['63330'],
            budget.total['63340'],
            budget.total['63430'] ]) }}
          </td>
        </tr>
        <tr v-for="(activity, key) in budget.activity" :key="budget.dkre + key">
          <td>{{ activity.name }}</td>
          <td>{{ activity.article['63430'] }}</td>
          <td>{{ addWithEmptyHelper([ activity.article['63310'], activity.article['63320'], activity.article['63330'],
            activity.article['63340'] ]) }}
          </td>
          <td>{{ activity.article['63310'] }}</td>
          <td>{{ activity.article['63320'] }}</td>
          <td>{{ activity.article['63330'] }}</td>
          <td>{{ activity.article['63340'] }}</td>
          <td>{{ addWithEmptyHelper([ activity.article['63310'], activity.article['63320'], activity.article['63330'],
            activity.article['63340'],
            activity.article['63430'] ]) }}
          </td>
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
        return this.$store.getters['budget/getBudget'];
      }
    },
    methods: {},
    mounted() {
      // this.$store.dispatch('budget/updateBudget', { period: 1, version: 2, isDkre: 1 })
    }
  }
</script>
